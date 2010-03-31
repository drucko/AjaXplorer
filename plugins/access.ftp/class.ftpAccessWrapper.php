<?php

require_once(INSTALL_PATH."/server/classes/interface.AjxpWrapper.php");

class ftpAccessWrapper implements AjxpWrapper {
	
	// Instance vars $this->
	protected $host;
	protected $port;
	protected $secure;
	protected $path;
	protected $user;
	protected $password;
	protected $ftpActive;
	protected $repoCharset;
	protected $repositoryId;
	protected $fp;
	
	// Shared vars self::
	private static $dirContent;
	private static $dirContentKeys;
	private static $dirContentIndex;
	protected static $repositoryConnexions; 
	
	
    public static function getRealFSReference($path){
    	$fake = new ftpAccessWrapper();
    	return $fake->buildRealUrl($path);
    }	
    
    public static function copyFileInStream($path, $stream){
    	$fake = new ftpAccessWrapper();
    	$parts = $fake->parseUrl($path);
		$link = $fake->createFTPLink();	
		$serverPath = AJXP_Utils::securePath($fake->path."/".$parts["path"]);
    	ftp_fget($link, $stream, $serverPath, FTP_BINARY);
    }
    
    public static function changeMode($path, $chmodValue){
    	$fake = new ftpAccessWrapper();
    	$parts = $fake->parseUrl($path);
		$link = $fake->createFTPLink();	
		$serverPath = AJXP_Utils::securePath($fake->path."/".$parts["path"]);
    	ftp_chmod($link, $chmodValue, $serverPath);
    }
    
	public function stream_open($url, $mode, $options, &$context){		
		if($mode == "w" || $mode == "rw"){
			$context = stream_context_create(array("ftp" => array("overwrite" => true)));
		}
		if($context){
			$this->fp = fopen($this->buildRealUrl($url), $mode, $options, $context);
		}else{
			$this->fp = fopen($this->buildRealUrl($url), $mode, $options);
		}
		return ($this->fp !== false);
	}
	
    function stream_stat(){
    	return fstat($this->fp);
    }	
    
    function stream_seek($offset, $option){
    	fseek($this->fp, $offset, SEEK_SET);
    }
    
    function stream_tell(){
    	return ftell($this->fp);
    }	
    
    function stream_read($count){
    	return fread($this->fp, $count);
    }

    function stream_write($data){
    	fwrite($this->fp, $data, strlen($data));
        return strlen($data);
    }

    function stream_eof(){
    	return feof($this->fp);
    }
   
    function stream_close(){
    	if(isSet($this->fp) && $this->fp!=-1 && $this->fp!==false){
    		fclose($this->fp);
    	}
    }
    
    function stream_flush(){
    	if(isSet($this->fp) && $this->fp!=-1 && $this->fp!==false){
	    	fflush($this->fp);
    	}
    }
    
    function unlink($url){
    	return unlink($this->buildRealUrl($url));
    }
    
    function rmdir($url){
    	return rmdir($this->buildRealUrl($url));
    }
    
    function mkdir($url){
    	return mkdir($this->buildRealUrl($url));
    }
    
    function rename($from, $to){
    	return rename($this->buildRealUrl($from), $this->buildRealUrl($to));
    }
    
    function url_stat($path, $flags){
    	// We are in an opendir loop
    	if(self::$dirContent != null){
    		$search = basename($path);
    		if(array_key_exists($search, self::$dirContent)){
	    		return self::$dirContent[$search];
    		}
    	}
    	$parts = $this->parseUrl($path);
		$link = $this->createFTPLink();	
		$serverPath = AJXP_Utils::securePath($this->path."/".$parts["path"]);		
		$testCd = @ftp_chdir($link, $serverPath);
		if($testCd === true){
			// DIR
			$serverParent = dirname($serverPath);
			$contents = $this->rawList($link, $serverParent);			
			foreach ($contents as $entry){
				$res = $this->rawListEntryToStat($entry);
				if($res["name"] == basename($serverPath)){
					$statValue = $res["stat"];
					return $statValue;
				}
			}
		}else{
			// FILE
			$contents = $this->rawList($link, $serverPath);		
	    	if(count($contents) == 1){
	    		$res = $this->rawListEntryToStat($contents[0]);
	    		if($res["name"] == $serverPath){
	    			$statValue = $res["stat"];
		    		return $statValue;
	    		}
	    	}
		}
    	return null;
    }
	
	public function dir_opendir ($url , $options ){
		$parts = $this->parseUrl($url);
		$link = $this->createFTPLink();	
		$serverPath = AJXP_Utils::securePath($this->path."/".$parts["path"]);		
		$contents = $this->rawList($link, $serverPath);
        $folders = $files = array();
		foreach($contents as $entry)
       	{
       		$result = $this->rawListEntryToStat($entry);
       		$isDir = $result["dir"];
       		$statValue = $result["stat"];
       		$file = $result["name"];
			if($isDir){
				$folders[$file] = $statValue;
			}else{
				$files[$file] = $statValue;
			}
       	}
		self::$dirContent = array_merge($folders, $files);
		self::$dirContentKeys = array_keys(self::$dirContent);
		self::$dirContentIndex = 0;			
		//print_r(self::$dirContent);
       	return true;
	}
	
	public function dir_closedir  (){
		self::$dirContent = null;
		self::$dirContentKeys = null;
		self::$dirContentIndex = 0;
	}	
	
	public function dir_readdir (){
		self::$dirContentIndex ++;
		if(isSet(self::$dirContentKeys[self::$dirContentIndex-1])){
			return self::$dirContentKeys[self::$dirContentIndex-1];
		}else{
			return false;
		}
	}	
	
	public function dir_rewinddir (){
		self::$dirContentIndex = 0;
	}
	    
	protected function rawList($link, $serverPath){
		$contents = @ftp_rawlist($link, $serverPath);
        if (!is_array($contents) && !$this->ftpActive) 
        {
            // We might have timed out, so let's go passive if not done yet
            global $_SESSION;
            if ($_SESSION["ftpPasv"] == "true"){
				return array();
            }
            @ftp_pasv($link, TRUE);
            $_SESSION["ftpPasv"]="true";
    		$contents = @ftp_rawlist($link, $serverPath);
            if (!is_array($contents)){
				return array();
            }
        }
        return $contents;		
	}
	
	protected function rawListEntryToStat($entry){
        $info = array();                              
		$vinfo = preg_split("/[\s]+/", $entry, 9);
		$statValue = array();
		if ($vinfo[0] !== "total")
       	{
	        $fileperms = $vinfo[0];                                         
			$info['num']   = $vinfo[1];
      		$info['owner'] = $vinfo[2];
      		$info['group'] = $vinfo[3];
      		$info['size']  = $vinfo[4];
      		$info['month'] = $vinfo[5];
      		$info['day']   = $vinfo[6];
      		$info['timeOrYear']  = $vinfo[7];
      		$info['name']  = $vinfo[8];
		 }
    	 $file = trim($info['name']);
		 $statValue[7] = $statValue["size"] = trim($info['size']);
		 if(strstr($info["timeOrYear"], ":")){
		 	$info["time"] = $info["timeOrYear"];
		 	$info["year"] = date("Y");
		 }else{
		 	$info["time"] = '09:00';
		 	$info["year"] = $info["timeOrYear"];
		 }
    	 $filedate  = trim($info['day'])." ".trim($info['month'])." ".trim($info['year'])." ".trim($info['time']);
    	 $statValue[9] = $statValue["mtime"]  = strtotime($filedate);
    	 
		 $isDir = false;
		 if (strpos($fileperms,"d")!==FALSE || strpos($fileperms,"l")!==FALSE)
		 {
			 if(strpos($fileperms,"l")!==FALSE)
			 {
    			$test=explode(" ->", $file);
				$file=$test[0];
		 	 }
		 	 $isDir = true;
		}
		$boolIsDir = $isDir;
		$statValue[2] = $statValue["mode"] = $this->convertingChmod($fileperms);
		$statValue["ftp_perms"] = $fileperms;
		return array("name"=>$file, "stat"=>$statValue, "dir"=>$isDir);
	}
	
	protected function parseUrl($url){
		// URL MAY BE ajxp.ftp://username:password@host/path
		$urlParts = parse_url($url);
		$this->repositoryId = $urlParts["host"];
		$repository = ConfService::getRepositoryById($this->repositoryId);		
		// Get USER/PASS
		// 1. Try from URL
		if(isSet($urlParts["user"]) && isset($urlParts["pass"])){
			$this->user = $urlParts["user"];
			$this->password = $urlParts["pass"];			
		}
		// 2. Try from user wallet
		if(!isSet($this->user) || $this->user==""){
			$loggedUser = AuthService::getLoggedUser();
			if($logged != null){
				$wallet = $loggedUser->getPref("AJXP_WALLET");
				if(is_array($wallet) && isSet($wallet[$repository->getId()]["FTP_USER"])){
					$this->user = $wallet[$repository->getId()]["FTP_USER"];
					$this->password = $wallet[$repository->getId()]["FTP_PASS"];
				}
			}
		}
		// 3. Try from repository config
		if(!isSet($this->user) || $this->user==""){
			$this->user = $repository->getOption("FTP_USER");
			$this->password = $repository->getOption("FTP_PASS");
		}
		if(!isSet($this->user) || $this->user==""){
			throw new AJXP_Exception("Cannot find user/pass for FTP access!");
		}
		$this->host = $repository->getOption("FTP_HOST");
		$this->path = $repository->getOption("PATH");
		$this->secure = ($repository->getOption("FTP_SECURE") == "TRUE"?true:false);
		$this->port = ($repository->getOption("FTP_PORT")!=""?intval($repository->getOption("FTP_PORT")):($this->secure?22:21));
		$this->ftpActive = ($repository->getOption("FTP_DIRECT") == "TRUE"?true:false);
		$this->repoCharset = $repository->getOption("CHARSET");
				
		// Test Connexion and server features
        global $_SESSION;
        $cacheKey = $repository->getId()."_ftpCharset";
        if (!isset($_SESSION[$cacheKey]) || !strlen($_SESSION[$cacheKey]))
        {
            $features = $this->getServerFeatures();
            if(!isSet($_SESSION["AJXP_CHARSET"]) || $_SESSION["AJXP_CHARSET"] == "") $_SESSION["AJXP_CHARSET"] = $features["charset"];
            $_SESSION[$cacheKey] = $_SESSION["AJXP_CHARSET"];
        }
        return $urlParts;
	}
	
	protected function buildRealUrl($url){
		if(!isSet($this->user)){
			$parts = $this->parseUrl($url);
		}else{
			// parseUrl already called before (rename case).
			$parts = parse_url($url);
		}
		$serverPath = AJXP_Utils::securePath("/$this->path/".$parts["path"]);
		return "ftp".($this->secure?"s":"")."://$this->user:$this->password@$this->host:$this->port".$serverPath;
	}	

    /** This method retrieves the FTP server features as described in RFC2389
     *	A decent FTP server support MLST command to list file using UTF-8 encoding
     *  @return an array of features (see code)
     */ 
    function getServerFeatures(){
    	$link = $this->createFTPLink();
        $features = @ftp_raw($link, "FEAT");        
        // Check the answer code
        if (isSet($features[0]) && $features[0][0] != "2"){
        	ftp_close($link);
        	return array("list"=>"LIST", "charset"=>$this->repoCharset);
        }
        $retArray = array("list"=>"LIST", "charset"=>$this->repoCharset);
        // Ok, find out the encoding used
        foreach($features as $feature)
        {
            if (strstr($feature, "UTF8") !== FALSE)
            {   // See http://wiki.filezilla-project.org/Character_Set for an explaination
                @ftp_raw($link, "OPTS UTF-8 ON");
                $retArray['charset'] = "UTF-8"; 
                ftp_close($link);
                return $retArray;
            }
        }
        // In the future version, we should also use MLST as it standardize the listing format
        return $retArray;
    }


    function createFTPLink(){
    	
    	// If connexion exist and is still connected
    	if(is_array(self::$repositoryConnexions) 
    		&& array_key_exists($this->repositoryId, self::$repositoryConnexions)
    		&& @ftp_systype(self::$repositoryConnexions[$this->repositoryId])){
    			return self::$repositoryConnexions[$this->repositoryId];
    		}
    	$link = FALSE;
   		//Connects to the FTP.          
   		if($this->secure){
   			$link = @ftp_ssl_connect($this->host, $this->port);
   		}else{
	        $link = @ftp_connect($this->host, $this->port);
   		}
        if(!$link) {
            throw new AJXP_Exception("Cannot connect to FTP server!");	               
 	    }
		register_shutdown_function('ftp_close', $link);
        @ftp_set_option($link, FTP_TIMEOUT_SEC, 10);
	    if(!@ftp_login($link,$this->user,$this->password)){
            throw new AJXP_Exception("Cannot login to FTP server with user $this->user");
        }
        if (!$this->ftpActive)
        {
            @ftp_pasv($link, true);
            global $_SESSION;
            $_SESSION["ftpPasv"]="true";
        }
        if(!is_array(self::$repositoryConnexions)){
        	self::$repositoryConnexions = array();
        }
        self::$repositoryConnexions[$this->repositoryId] = $link;
        return $link;
    }	
    
	function convertingChmod($permissions, $filterForStat = false)
	{
		$mode = 0;
		
		if ($permissions[1] == 'r') $mode += 0400;
		if ($permissions[2] == 'w') $mode += 0200;
		if ($permissions[3] == 'x') $mode += 0100;		
	 	else if ($permissions[3] == 's') $mode += 04100;
	 	else if ($permissions[3] == 'S') $mode += 04000;
	
	 	if ($permissions[4] == 'r') $mode += 040;
	 	if ($permissions[5] == 'w' || ($filterForStat && $permissions[2] == 'w')) $mode += 020;
	 	if ($permissions[6] == 'x' || ($filterForStat && $permissions[3] == 'x')) $mode += 010;
	 	else if ($permissions[6] == 's') $mode += 02010;
	 	else if ($permissions[6] == 'S') $mode += 02000;
	
	 	if ($permissions[7] == 'r') $mode += 04;
	 	if ($permissions[8] == 'w' || ($filterForStat && $permissions[2] == 'w')) $mode += 02;
	 	if ($permissions[9] == 'x' || ($filterForStat && $permissions[3] == 'x')) $mode += 01;
	 	else if ($permissions[9] == 't') $mode += 01001;
	 	else if ($permissions[9] == 'T') $mode += 01000;	
	 	
		if($permissions[0] != "d") {
			$mode += 0100000;
		}else{
			$mode += 0040000;
		}
	 	
		$mode = (string)("0".$mode);	
		return  $mode;
	}
    
}
?>