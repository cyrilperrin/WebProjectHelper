<?php

/**
 * Class to logg events
 * @author Cyril Perrin
 * @license LGPL v3
 * @version 2013-01-05
 */
class Logger
{
	// Savers
	private $savers = array();

	// Errors
	private $errorsDisplay;
	private $errorsSave;
	private $errorsStop;
	private $errorsRedirect = array();
	private $errorsRedirectPage = array();
	private $errorsRedirectCallBack = array();

	// Exceptions
	private $exceptionsDisplay;
	private $exceptionsRedirectPage;
	private $exceptionsRedirectCallBack;

	// Categories
	const ERROR = 1;
	const EXCEPTION = 2;
	const MESSAGE = 3;

	/**
	 * Constructor
	 * @param $savers ILogSaver log savers
	 * @param $displayErrors int error types must be displayed, use bit operators to combine values, default : E_ALL&~E_NOTICE
	 * @param $saveErrors int error type must be saved, use bit operators to combine values, default : E_ALL&~E_NOTICE
	 * @param $stopErrors int error type must stop script execution, use bit operators to combine values, default : E_ALL&~E_NOTICE
	 * @param $displayExceptions bool display exceptions ?
	 */
	public function __construct(ILogSaver $savers,$displayErrors=30711,$saveErrors=30711,$stopErrors=30711,$displayExceptions=true)
	{
		$this->savers[] = $savers;
		$this->errorsDisplay = $displayErrors;
		$this->errorsSave = $saveErrors;
		$this->errorsStop = $stopErrors;
		$this->exceptionsDisplay = $displayExceptions;
	}

	/**
	 * Add an entry into data
	 * @param $category ? category of entry
	 * @param $log array information about entry
	 * @param $error bool is it an error ?
	 */
	private function add($category,$log)
	{
		// Complete information array
		$log['ip'] = $_SERVER['REMOTE_ADDR'];
		$log['url'] = urldecode($_SERVER['REQUEST_URI']);

		// Add information
		foreach($this->savers as $saver) {
			$saver->add($category,$log);
		}
	}

	/**
	 * Add a simple message into data
	 * @param $category string category of message
	 * @param $message string message
	 */
	public function addMessage($category,$message)
	{
		// Prepare log array
		$log = array(
			'category' => $category,
			'message' => $message
		);

		// Add log
		$this->add(Logger::MESSAGE,$log);
	}

	/**
	 * Handling error method
	 * @param $code int error code
	 * @param $message string error message
	 * @param $file string error file
	 * @param $line int error line
	 * @return bool send error to default handler ?
	 */
	public function addError($code, $message, $file, $line)
	{
		// Save error if necessary
		if($this->errorsSave&$code) {
			// Prepare log array
			$log = array(
				'code' => $code,
				'message' => $message,
				'file' => $file,
				'line' => $line
			);
				
			// Add log
			$this->add(Logger::ERROR,$log);
		}

		// Redirect if necessary
		if(isset($this->errorsRedirect)) {
			foreach($this->errorsRedirect as $key => $errorsRedirect) {
				if($errorsRedirect&$code) {
					call_user_func($this->errorsRedirectCallBack[$key],$this->errorsRedirectPage[$key]);
				}
			}
		}

		// Display if necessary
		if($this->errorsDisplay&$code) {
			echo '<p><b>Error ('.$code.') !</b> '.$message.' in file <b>'.$file.'</b> at line <b>'.$line.'</b></p>',
			     '<p><b>Debug backtrace :</b></p><pre>'.print_r(debug_backtrace(),true).'</pre>';
		}

		// Stop script execution if necessary
		if($this->errorsStop&$code) {
			exit;
		}

		// Don't call default php handler
		return true;
	}

	/**
	 * Handling exception method
	 * @param $exception Exception exception
	 */
	public function addException(Exception $exception)
	{
		// Prepare log array
		$log = array(
			'code' => $exception->getCode(),
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine()
		);

		// Add log
		$this->add(Logger::EXCEPTION,$log);

		// Redirect if necessary
		if(isset($this->exceptionsRedirectCallBack,$this->exceptionsRedirectPage)) {
			call_user_func($this->exceptionsRedirectCallBack,$this->exceptionsRedirectPage);
		}

		// Display if necessary
		if($this->exceptionsDisplay) {
			echo '<p><b>',get_class($exception),' ('.$exception->getCode().') !</b> '.$exception->getMessage().' in file <b>'.$exception->getFile().'</b> at line <b>'.$exception->getLine().'</b></p>',
			     '<p><b>Debug backtrace :</b></p><pre>'.print_r(debug_backtrace(),true).'</pre>';
		}
	}

	/**
	 * Add data object
	 * @param ILogSaver $saver object will be called to add message
	 */
	public function addLogData(ILogSaver $saver)
	{
		$this->savers[] = $saver;
	}

	/**
	 * Add page wich user will be redirected when errors given happen
	 * @param $page string page where visitor will be redirected when errors happen
	 * @param $callBack callback callback will be called to redirect user
	 * @param $errors int error types, use bit operators to combine values, default : E_ALL&~E_NOTICE
	 */
	public function addErrorsPage($page,$callBack,$errors=30711)
	{
		if(is_callable($callBack)) {
			$this->errorsRedirect[] = $errors;
			$this->errorsRedirectPage[] = $page;
			$this->errorsRedirectCallBack[] = $callBack;
		} else {
			throw new Exception('CallBack for errors page given isn\'t correct.');
		}
	}

	/**
	 * Add page wich user will be redirected when exceptions happen
	 * @param $page string page where user will be redirected when errors happen
	 * @param $callBack callback callback will be called to redirect user
	 */
	public function setExceptionsPage($page,$callBack)
	{
		if(is_callable($callBack)) {
			$this->exceptionsRedirectPage = $page;
			$this->exceptionsRedirectCallBack = $callBack;
		} else {
			throw new Exception('CallBack given for exceptions page isn\'t correct.');
		}
	}
}

/**
 * Interface to implement to be considered as a log strategy
 */
interface ILogSaver
{
	/**
	 * Add a log into data structure
	 * @param $category ? category (Logger::ERROR, Logger::EXCEPTION, Logger::MESSAGE)
	 * @param $array array data array
	 */
	public function add($category,$array);
}

/**
 * Strategy to log into files
 */
class LogSaver_File implements ILogSaver
{

	// File path and handle
	protected $filePath;
	protected $fileHandle;

	// Constructor/destructor

	/**
	 * Constructor
	 * @param $folder string folder path of log files, ending by "/"
	 * @param $duration int duration of an log file in days
	 * @param $nbMax int maximum number of log files, -1 for unlimited
	 */
	public function __construct($folder,$duration=1,$nbMax=7)
	{
		// Open directory
		if(!is_dir($folder)) {
			throw new Exception('Folder \''.$folder.'\' doesn\'t exist.');
		}
		if(!($directory = opendir($folder))) {
			throw new Exception('Permission denied to read into folder \''.$folder.'\'.');
		}

		// List log files
		$tabFiles = array();
		while(($entry = readdir($directory)) !== false) {
			// List files
			if(sscanf(pathinfo($entry,PATHINFO_BASENAME),'%d-%d-%d.'.$this->getExtension(),$year,$month,$day) == 3) {
				// Log file !
				$tabFiles[mktime(0,0,0,$month,$day,$year)] = $folder.$entry;
			}
		}

		// Find path of current log file or "create" it
		if(count($tabFiles)) {
			ksort($tabFiles); // Sort files array by key (timestamp)
			reset($tabFiles); // Reset pointer
			if(time()-key($tabFiles) <= $duration*24*3600) {
				$this->filePath = current($tabFiles);
			}
		}
		if(!isset($this->filePath)) {
			// If file not found
			$this->filePath = $folder.date('Y-m-d').'.'.$this->getExtension();
		}

		// Clean log files folder
		if(count($tabFiles) && $nbMax != -1) {
			krsort($tabFiles); // Sort files array by key (timestamp)
			while(count($tabFiles) > $nbMax) {
				$oldFile = array_pop($tabFiles); // Delete old file in array
				unlink($oldFile); // Delete old file in files system
			}
		}
	}

	/**
	 * Get file extension
	 * @return string file extension
	 */
	public function getExtension()
	{
		return 'log';
	}

	/**
	 * @see ILogSaver::add()
	 */
	public function add($category,$array)
	{
		// Open file if necessary
		if(!isset($this->fileHandle)) {
			if(!($this->fileHandle = fopen($this->filePath,'a+'))) {
				throw new Exception('Permission denied to write into file \''.$this->filePath.'\'.');
			}
		}

		// Prepare string
		$temp = array();
		foreach($array as $name => $value) {
			$temp[] = $name.' = "'.$value.'"';
		}
		if($category == Logger::ERROR) {
			$categoryTxt = 'error';
		}
		elseif($category == Logger::EXCEPTION) {
			$categoryTxt = 'exception';
		}
		elseif($category == Logger::MESSAGE) {
			$categoryTxt = 'message';
		}
		else { $categoryTxt = 'unknown';
		}
		$string = '['.date('D, j M Y H:i:s').'|'.time().'] <'.$categoryTxt.'> '.implode(' ',$temp);

		// Add message
		fputs($this->fileHandle,$string.';'.PHP_EOL);
	}
}

/**
 * Strategy to log into XML files
 */
class LogSaver_File_XML extends LogSaver_File
{

	// Dom elements
	private $dom;
	private $exceptions;
	private $errors;
	private $messages;

	// Style sheet
	private $stylesheet;

	/**
	 * @see LogSaver_File::getExtension()
	 */
	public function getExtension()
	{
		return 'xml';
	}

	/**
	 * @see LogSaver_File::add()
	 */
	public function add($category,$array)
	{
		// Load xml if necessary
		if(!isset($this->dom)) {
			$this->dom = new DomDocument('1.0');
			$this->dom->formatOutput = true;
			if(file_exists($this->filePath)) {
				// File already exists
				// Load document
				$this->dom->load($this->filePath);

				// Get exceptions, errors and messages nodes
				$this->exceptions = $this->dom->getElementsByTagName('exceptions')->item(0);
				$this->errors = $this->dom->getElementsByTagName('errors')->item(0);
				$this->messages = $this->dom->getElementsByTagName('messages')->item(0);
			} else { // File doesn't exists
				// Attache style sheet
				if($this->stylesheet != null) {
					$xsl = $this->dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="'.$this->stylesheet.'"');
					$this->dom->appendChild($xsl);
				}

				// Create root
				$root = $this->dom->createElement('logs');
				$this->dom->appendChild($root);

				// Create exceptions, errors and messages nodes
				$this->exceptions = $this->dom->createElement('exceptions');
				$this->errors = $this->dom->createElement('errors');
				$this->messages = $this->dom->createElement('messages');
				$root->appendChild($this->exceptions);
				$root->appendChild($this->errors);
				$root->appendChild($this->messages);
			}
		}

		// Create element
		if($category == Logger::EXCEPTION) {
			$element = $this->dom->createElement('exception');
		}
		elseif($category == Logger::ERROR) {
			$element = $this->dom->createElement('error');
		} else {
			$element = $this->dom->createElement('message');
		}

		// Fill element
		$element->setAttribute('date',date('D, j M Y H:i:s'));
		$element->setAttribute('time',time());
		foreach($array as $key => $value) {
			$element->setAttribute(utf8_encode($key),utf8_encode(strip_tags($value)));
		}

		// Insert element
		if($category == Logger::EXCEPTION) {
			$categoryNode = $this->exceptions;
		}
		elseif($category == Logger::ERROR) {
			$categoryNode = $this->errors;
		}
		else {
			$categoryNode = $this->messages;
		}
		$categoryNode->appendChild($element);

		// Save entry
		$this->dom->save($this->filePath);
	}

	/**
	 * Associate style sheet
	 * @param $filename string style sheet filename
	 */
	public function setStyleSheet($filename)
	{
		$this->stylesheet = $filename;
	}

}