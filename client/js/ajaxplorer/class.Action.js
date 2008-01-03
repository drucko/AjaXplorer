Action = Class.create({

	initialize:function(){
		this.options = Object.extend({
			name:'',
			src:'',
			text:'',
			title:'',
			hasAccessKey:true,
			accessKey:'',
			callbackCode:'',
			callback:Prototype.emptyFunction,
			prepareModal:false, 
			formId:undefined, 
			formCode:undefined
			}, arguments[0] || { });
		this.context = Object.extend({
			selection:true,
			dir:false,
			recycle:false,			
			actionBar:false,
			actionBarGroup:'default',
			contextMenu:false,
			infoPanel:false			
			}, arguments[1] || { });
			
		this.selectionContext = Object.extend({			
			dir:false,
			file:true,
			recycle:false,
			unique:true,
			image:true,
			mp3:true,
			editable:true,			
			}, arguments[2] || { });
		this.rightsContext = Object.extend({			
			noUser:true,
			userLogged:true,			
			read:true,
			write:false,
			adminOnly:false
			}, arguments[3] || { });
		
		this.elements = new Array();
		this.contextHidden = false;
		this.deny = false;
	}, 
	
	apply: function(){
		if(this.deny) return;
		if(this.options.prepareModal){
			modal.prepareHeader(this.options.title, ajxpResourcesFolder+'/images/crystal/actions/16/'+this.options.src);
		}
		window.actionArguments = $A([]);
		if(arguments[0]) window.actionArguments = $A(arguments[0]);
		if(this.options.callbackCode) this.options.callbackCode.evalScripts();
		window.actionArguments = null;
	},
	
	fireContextChange: function(){
		if(arguments.length < 3) return;
		var usersEnabled = arguments[0]
		var crtUser = arguments[1];
		var crtIsRecycle = arguments[2];
		var rightsContext = this.rightsContext;
		if(!rightsContext.noUser && !usersEnabled){
			return this.hideForContext();				
		}
		if(rightsContext.userLogged == 'only' && crtUser == null){
			return this.hideForContext();
		}
		if(rightsContext.userLogged == 'hidden' && crtUser != null){
			return this.hideForContext();
		}
		if(rightsContext.adminOnly && (crtUser == null || crtUser.id != 'admin')){
			return this.hideForContext();
		}
		if(rightsContext.read && crtUser != null && !crtUser.canRead()){
			return this.hideForContext();
		}
		if(rightsContext.write && crtUser != null && !crtUser.canWrite()){
			return this.hideForContext();
		}
		if(this.context.recycle){
			if(this.context.recycle == 'only' && !crtIsRecycle){
				return this.hideForContext();				
			}
			if(this.context.recycle == 'hidden' && crtIsRecycle){
				return this.hideForContext();
			}
		}			
		this.showForContext();				
		
	},
		
	fireSelectionChange: function(){
		if(arguments.length < 1 
			|| this.contextHidden 
			|| !this.context.selection) {	
			return;
		}
		var userSelection = arguments[0];		
		var bSelection = false;
		if(userSelection != null) 
		{			
			bSelection = !userSelection.isEmpty();
			var bUnique = userSelection.isUnique();
			var bFile = userSelection.hasFile();
			var bDir = userSelection.hasDir();
			var bImage = userSelection.isImage();
			var bEditable = userSelection.isEditable();
			var bRecycle = userSelection.isRecycle();
		}
		var selectionContext = this.selectionContext;
		if(selectionContext.image || selectionContext.editable) {
			this.hide();
		}
		if(selectionContext.unique && !bUnique){
			return this.disable();
		}
		if((selectionContext.file || selectionContext.dir) && !bFile && !bDir){
			return this.disable();
		}
		if((selectionContext.dir && !selectionContext.file && bFile) 
			|| (!selectionContext.dir && selectionContext.file && bDir)){
			return this.disable();
		}
		if(!selectionContext.recycle && bRecycle){
			return this.disable();
		}
		if(selectionContext.image && !bImage){
			return this.hide();
		} 
		if(selectionContext.editable && !bEditable){
			return this.hide();		
		}
		this.show();
		this.enable();
		
	},
		
	createFromXML:function(xmlNode){
		this.options.name = xmlNode.getAttribute('name');
		for(var i=0; i<xmlNode.childNodes.length;i++){
			var node = xmlNode.childNodes[i];
			if(node.tagName == "processing"){
				for(var j=0; j<node.childNodes.length; j++){
					var processNode = node.childNodes[j];
					if(processNode.tagName == "clientForm"){
						this.options.formId = processNode.getAttribute("id");
						this.options.formCode = processNode.firstChild.nodeValue;
						this.insertForm();
					}else if(processNode.tagName == "clientCallback"){
						this.options.callbackCode = '<script>'+processNode.firstChild.nodeValue+'</script>';
						if(processNode.getAttribute('prepareModal') && processNode.getAttribute('prepareModal') == "true"){
							this.options.prepareModal = true;
						} 
					}
				}
			}else if(node.tagName == "gui"){
				this.options.text = MessageHash[node.getAttribute('text')];
				this.options.title = MessageHash[node.getAttribute('title')];
				this.options.src = node.getAttribute('src');
				this.options.accessKey = node.getAttribute('accessKey');
				for(var j=0; j<node.childNodes.length;j++){
					if(node.childNodes[j].tagName == "context"){
						this.attributesToObject(this.context, node.childNodes[j]);
					}
					else if(node.childNodes[j].tagName == "selectionContext"){
						this.attributesToObject(this.selectionContext, node.childNodes[j]);
					}
				}
							
			}else if(node.tagName == "rightsContext"){
				this.attributesToObject(this.rightsContext, node);
			}			
		}
	}, 
	
	toActionBar:function(){
		var button = new Element('a', {
			href:this.options.name,
			id:this.options.name +'_button'
		}).observe('click', function(e){
			Event.stop(e);
			this.apply();
		}.bind(this));
		var imgPath = 'client/images/crystal/actions/22/'+this.options.src;
		var img = new Element('img', {
			src:imgPath,
			width:22,
			height:22,
			border:0,
			align:'absmiddle',
			alt:this.options.title,
			title:this.options.title
		});
		var titleSpan = new Element('span');
		button.insert(img).insert(new Element('br')).insert(titleSpan.update(this.options.text));		
		this.elements.push(button);
		return button;
	},
	
	toInfoPanel:function(){
	
	},
	
	toContextMenu:function(){
		return this.options;
	},
	
	hideForContext: function(){
		this.hide();
		this.contextHidden = true;
	},
	
	showForContext: function(){
		this.show();
		this.contextHidden = false;
	},
	
	hide: function(){		
		if(this.elements.size() > 0) this.deny = true;
		this.elements.each(function(elem){
			elem.hide();
		});
	},
	
	show: function(){
		if(this.elements.size() > 0) this.deny = false;
		this.elements.each(function(elem){
			elem.show();
		});
	},
	
	disable: function(){
		if(this.elements.size() > 0) this.deny = true;
		this.elements.each(function(elem){
			elem.addClassName('disabled');
		});	
	},
	
	enable: function(){
		if(this.elements.size() > 0) this.deny = false;
		this.elements.each(function(elem){
			elem.removeClassName('disabled');
		});	
	},
	
	remove: function(){
		// Remove all elements and forms from html
		this.elements.each(function(el){
			$(el).remove();
		});
		if(this.options.formId && $('all_forms').select('[id="'+this.options.formId+'"]').length){
			$('all_forms').select('[id="'+this.options.formId+'"]')[0].remove();
		}
	},
	
	insertForm: function(){
		if(!this.options.formCode || !this.options.formId) return;
		if($('all_forms').select('[id="'+this.options.formId+'"]').length) return;
		$('all_forms').insert(this.options.formCode);
	},
	
	attributesToObject: function(object, node){
		Object.keys(object).each(function(key){
			if(node.getAttribute(key)){
				value = node.getAttribute(key);
				if(value == 'true') value = true;
				else if(value == 'false') value = false;
				this[key] = value;
			}
		}.bind(object));
	}

});