<?php

/**
 * Index controller
 */
class IndexController extends AbstractController
{
	/**
	 * Action for index
	 */
	public function indexAction()
	{
		// Get counter and send it to view
		$this->view->counter = file_get_contents(ROOT_DIR.'data/counter.txt');
		
		// Render
		$this->render();
	}
	
	/**
	 * Action to change language
	 */
	public function languageAction()
	{
		// Store language
		$_SESSION['language'] = $this->request->getParameter('language');
		
		// Redirect to index
		Request::redirect(Request::getBase());
	}
	
	/**
	 * Action to generate PHP classes or MySQL script
	 */
	public function generateAction()
	{
		// Force 200 status...
		header('Status: 200');
		
		// Check form is complete
		if (!isset($_POST['definitions'])) { die('<p class="message m_error">'.tr('The form is not complete').'</p>'); }
		
		// Check elements definitions are present
		if ($_POST['definitions'] == '') { die('<p class="message m_error">'.tr('No definition of elements has been given').'</p>'); }
		
		// Strip slashes
		if (get_magic_quotes_gpc()){ $_POST['definitions'] = stripslashes($_POST['definitions']); }
		
		try {
			// Load and compile project
			$project = Loader::load($_POST['definitions'])->compile();
		} catch(Exception $e) {
			// Display error
			die('<p class="message m_error">'.tr($e->getMessage()).'</p>');
		}
		
		// Compile ?
		if (!empty($_POST['compile'])) {
			// Check form is complete
			if (!isset($_POST['zipphp'],$_POST['zipsql'])) {
				die('<p class="message m_error">'.tr('The form is not complete').'</p>');
			}
			
			// Update counter
			$nb_generations = file_get_contents(ROOT_DIR.'data/counter.txt')+1;
			file_put_contents(ROOT_DIR.'data/counter.txt',$nb_generations);
			
			// Display links
			if ($_POST['zipsql'] != 'true') {
				echo '<a href="#" onclick="generateSQL();return false;" class="button b_sql">'.tr('MySQL script').'</a>';
			} else {
				echo '<a href="#" onclick="generateSQL();return false;" class="button b_zip">'.tr('MySQL '.($project->count() > 1 ? 'scripts' : 'script')).'</a>';
			}
			if ($_POST['zipphp'] != 'true') {
				echo '<a href="#" onclick="generatePHP();return false;" class="button b_php">'.tr('PHP '.($project->count() > 1 ? 'classes' : 'class')).'</a>';
			} else {
				echo '<a href="#" onclick="generatePHP();return false;" class="button b_zip">'.tr('PHP '.($project->count() > 1 ? 'classes' : 'class')).'</a>';
			}
			
			// Save definitions
			$fd = fopen(ROOT_DIR.'data/definitions.txt','a');
			fwrite($fd,str_repeat('-',30).' '.date('d/m/Y').' '.str_repeat('-',30)."\n".$_POST['definitions']."\n");
			fclose($fd);
		} else {	
			// Check form is complete
			if (!isset($_POST['generate'],$_POST['zip'])) { die('<p class="message m_error">'.tr('Manipulation error').'</p>'); }
			
			// MySQL config
			MySQL::set_prefix($_POST['prefixdatatables']);
			
			// PHP config
			if($_POST['generate'] == 'php') {
				PHP::set_fieldnames_into_base_classes($_POST['fieldnamesbase'] == 'true');
				PHP::set_generate_only_base_classes($_POST['onlybase'] == 'true');
				PHP::set_classes_prefix($_POST['phpclassesprefix']);
				PHP::set_files_prefix($_POST['phpfilesprefix']);
			}
			
			// Create MySQL/PHP geneators
			$mysql = new MySQL_Generator($project);
			if ($_POST['generate'] != 'sql') { $php = new PHP_Generator($project,$mysql); }
			
			// Generate MySQL structure and PHP classes
		  	header("Content-type: application/force-download");
			if ($_POST['zip'] == 'false') {
				// Change header 
				header('Content-disposition: attachment; filename='.($_POST['generate'] == 'sql'? 'script.sql':($project->count() > 1 ? 'classes' : 'class').'.php'));
				
				// Display MySQL script/PHP classes
				if ($_POST['generate'] == 'sql') { echo $mysql->generate_tables(); }
				else { echo nl('<?php').nl().$php->generate_classes().nl(); }
			} else {
				// Change header
				header('Content-disposition: attachment; filename='.($_POST['generate'] == 'sql'?'mysql.zip':'php.zip'));
				
				// Generate zip
				if ($_POST['generate'] == 'sql') { echo $mysql->generate_zip(); }
				else { echo $php->generate_zip(); }
			}
		}
	}
}