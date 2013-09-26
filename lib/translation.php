<?php

/**
 * Translation tool
 * @author Cyril Perrin
 * @license LGPL v3
 * @version 2013-09-15
 */
class Translation
{
	
	/** @var $singleton Translation translation tool single instance  */
	private static $singleton;
	
	/** @var $filename ITranslatedSentencesProvider sentences provider */
	private $sentences;
	
	/** @var $store IStoredLanguageProvider stored language provider */
	private $store;
	
	/** @var $provider ILanguageProvider language provider */
	private $provider;
	
	/**
	 * Constructor
	 * @param $sentences ITranslatedSentencesProvider sentences provider
	 * @param $store IStoredLanguageProvider stored language provider
	 * @param $provider ILanguageProvider language provider
	 * @param $default string default language
	 */
	private function __construct(ITranslatedSentencesProvider $sentences, IStoredLanguageProvider $store, ILanguageProvider $provider, $default) {
		// Save language, stored language and sentences providers
		$this->sentences = $sentences;
		$this->store = $store;
		$this->provider = $provider;
		
		// Init sentences provider
		$this->sentences->init($this->language($default));
	}
	
	/**
	 * Get language
	 * @param $default default language
	 */
	public function language($default='en') {
		// Get language from store
		$language = $this->store->get();
		
		// If store is empty
		if($language === null) {
			// Get language from language provider
			$language = $this->provider->get($default);
			
			// Store language
			$this->store->store($language);
		}
		
		// Return language
		return $language;
	}
	
	/**
	 * Translate a sentence
	 * @param $parameters ? parameters to send to sentences provider
	 */
	public function tr($parameters) {
		return $this->sentences->get($parameters);
	}
	
	/**
	 * Initialize translation tool
	 * @param $sentences ITranslatedSentencesProvider sentences provider
	 * @param $store IStoredLanguageProvider stored language provider
	 * @param $provider ILanguageProvider language provider
	 * @param $default string default language
	 */
	public static function init(ITranslatedSentencesProvider $sentences,$store=null,$provider=null,$default='en') {
		// Get default providers if null
		if(!$provider) { $provider  = new LanguageProvider_Browser(); }
		if(!$store) { $store = new StoredLanguageProvider_Session(); }
		
		// Initialize translation tool
		Translation::$singleton = new Translation($sentences,$store,$provider,$default);
	}
	
	/**
	 * Get translation tool single instance
	 */
	public static function get() {
		return Translation::$singleton;
	}
}

/**
 * Interface to implement to be considered as language provider
 */
interface ILanguageProvider {
	
	/**
	 * Get language
	 * @param $default string default language if language is not defined 
	 * @return string language
	 */
	public function get($default=null);
	
}

/**
 * Language provider from variable
 */
class LanguageProvider_Variable implements ILanguageProvider {
	
	/** @var $language string language */
	private $language;
	
	/**
	 * Constructor
	 */
	public function __construct($language) {
		$this->language = $language;
	}
	
	/**
	 * @see ILanguageProvider::get()
	 */
	public function get($default=null) {
		return $this->language;
	}
	
}

/**
 * Language provider from browser
 */
class LanguageProvider_Browser implements ILanguageProvider {
	
	/**
	 * @see ILanguageProvider::get()
	 */
	public function get($default=null) {		
		// Check if browser send language in HTTP request
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// Search language
			return strtolower(current(explode('-',current(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE'])))));
		} else {
			// Return default
			return $default;
		}
	}
	
}

/**
 * Interface to implement to be considered as stored language provider
 */
interface IStoredLanguageProvider {
	
	/**
	 * Get stored language
	 * @return string language or null if not defined
	 */
	public function get();
	
	/**
	 * Store a language
	 * @param $language string language to store
	 */
	public function store($language);
	
}

/**
 * Stored language provider from session
 */
class StoredLanguageProvider_Session implements IStoredLanguageProvider {
	
	/** @var $init string initial language */
	private $init;
	
	/** @var $redirector string callback to redirect user if language is not set */
	private $redirector;
	
	/** @var $session string session name */
	private $session;
	
	/**
	 * Constructor
	 * @param $init string default initial language
	 * @param $redirect string callback to redirect user if language is not set
	 * @param $session string session name
	 */
	public function __construct($init=null,$redirector=null,$session='language') {
		// Save init and redirector
		$this->init = $init;
		$this->redirector = $redirector;
		$this->session = $session;
		
		// Redirect user if necessary
		if (isset($_SESSION[$this->session]) && empty($this->init) && $this->redirector != null) {
			call_user_func($this->redirector,$_SESSION[$this->session]);
		}
		
		// Store language if necessary
		if(!empty($init)) {
			$this->store($this->init);
		} 
	}
	
	/**
	 * @see IStoredLanguageProvider::get()
	 */
	public function get() {
		// Try from init
		if (!empty($this->init)) {
			return $this->init;
		}
		
		// Try from session
		if (isset($_SESSION[$this->session])) {
			return $_SESSION[$this->session];
		}
		
		// Return null
		return null;
	}
	
	/**
	 * @see IStoredLanguageProvider::store()
	 */
	public function store($language) {
		// Store language
		$_SESSION[$this->session] = $language;
		
		// Redirect user if necessary
		if (empty($this->init) && $this->redirector != null) {
			call_user_func($this->redirector,$language);
		}
	}
	
} 

/**
 * Interface to implement to be considered as sentences provider
 */
interface ITranslatedSentencesProvider {
	
	/**
	 * Init target language
	 * @param $language string language
	 */
	public function init($language);
	
	/**
	 * Get sentence
	 * @param $parameters ? parameters given by user
	 */
	public function get($parameters);
	
}

/**
 * Translated sentences from file
 */
class TranslatedSentencesProvider_File implements ITranslatedSentencesProvider {
	
	/** @var $reference int reference language index */
	private $reference;
	
	/** @var $language int language index */
	private $language;
	
	/** @var $languages array languages */
	private $languages;
	
	/** @var $sentences array sentences */
	private $sentences;
	
	/**
	 * Constructor
	 * @param $filename string translations file path
	 * @param $index int reference language index
	 */
	public function __construct($filename, $reference=0) {
		// Save index
		$this->reference = $reference;
		
		// Get sentences
		$this->sentences = file($filename);
		
		// Parse sentences
		foreach($this->sentences as $key => $sentences) {
			$this->sentences[$key] = explode("\t",trim($sentences));
		}
		
		// Get languages
		$this->languages = array_shift($this->sentences);
	}
	
	/**
	 * @see ITranslatedSentencesProvider::init()
	 */
	public function init($language) {
		// Search language index in languages list
		$this->language = array_search($language, $this->languages);
	}
	
	/**
	 * @see ITranslatedSentencesProvider::get()
	 */
	public function get($sentence) {		
		// Return initial sentence if language is reference
		if ($this->language == $this->reference) { return str_replace('\\','',$sentence); }
		
		// Search a correspondance in sentences
		foreach ($this->sentences as $translations) {
			// Correspondance ?
			if (preg_match('/^'.$translations[$this->reference].'$/i',$sentence)) {
				// Check if a translation exists
				if (!empty($translations[$this->language])) {
					return preg_replace('/^'.$translations[$this->reference].'$/i', $translations[$this->language], $sentence);
				}
				
				// Return initial sentence if no translation exists
				return str_replace('\\','',$sentence);
			}
		}
		
		// Return initial sentence if no correspondance found
		return str_replace('\\','',$sentence);
	}
	
}

/**
 * Translate a sentance
 * @param $parameters parameters to send to translator
 * @return string translated sentance
 */
function tr($parameters) {
	return Translation::get()->tr($parameters);
}
