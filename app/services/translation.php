<?php

/**
 * Database connexion service
 */
class TranslationService extends Service
{
	
	/**
	 * @see Service::start()
	 */
	public function start() {
		Translation::init(
			new TranslatedSentencesProvider_File(ROOT_DIR.'data/translations.txt'),
			new StoredLanguageProvider_Session(isset($_SESSION['language']) ? $_SESSION['language'] : null,null,'language')
		);
	}
	
	/**
	 * @see Service::getRessource()
	 */
	public function getRessource() {
		return Translation::get();
	}
	
	/**
	 * @see Service::stop()
	 */
	public function stop() {
		
	}
}