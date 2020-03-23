<?php

/**
 * @file plugins/themes/NUimmersion/NUimmersionThemePlugin.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NUimmersionThemePlugin
 * @ingroup plugins_themes_NUimmersion
 *
 * @brief NUimmersion theme
 */

import('lib.pkp.classes.plugins.ThemePlugin');
class NUimmersionThemePlugin extends ThemePlugin {

	public function init() {

		$this->addStyle(
			'fonts',
			'https://fonts.googleapis.com/css?family=Roboto:300,400,400i,700,700i|Spectral:400,400i,700,700i',
			array('baseUrl' => ''));

		// Adding styles (JQuery UI, Bootstrap, Tag-it)
		//$this->addStyle('app-css', 'resources/dist/app.min.css');
		$this->addStyle('bootstrap', 'resources/dist/bootstrap.min.css');
		$this->addStyle('fontawesome', 'resources/dist/font-awesome.min.css');
		$this->addStyle('style', 'resources/dist/style.css');
		$this->addStyle('mystyle', 'resources/dist/mystyle.css');
		$this->addStyle('less', 'resources/less/import.less');

		// Styles for HTML galleys
		$this->addStyle('htmlGalley', 'templates/plugins/generic/htmlArticleGalley/css/default.css', array('contexts' => 'htmlGalley'));

		// Adding scripts (JQuery, Popper, Bootstrap, JQuery UI, Tag-it, Theme's JS)
		$this->addScript('app-js', 'resources/dist/app.min.js');
		$this->addScript('boot-js', 'resources/dist/bootstrap.bundle.min.js');
		$this->addScript('blocs-js', 'resources/dist/blocs.min.js');
		$this->addScript('jquery-js', 'resources/dist/jquery-3.3.1.min.js');
		$this->addScript('lazysites-js', 'resources/dist/lazysizes.min.js');
		

		// Add navigation menu areas for this theme
		$this->addMenuArea(array('primary', 'user'));

		// Option to show section description on the journal's homepage; turned off by default
		$this->addOption('sectionDescriptionSetting', 'radio', array(
			'label' => 'plugins.themes.NUimmersion.options.sectionDescription.label',
			'description' => 'plugins.themes.NUimmersion.options.sectionDescription.description',
			'options' => array(
				'disable' => 'plugins.themes.NUimmersion.options.sectionDescription.disable',
				'enable' => 'plugins.themes.NUimmersion.options.sectionDescription.enable'
			)
		));

		$this->addOption('journalDescription', 'radio', array(
			'label' => 'plugins.themes.NUimmersion.options.journalDescription.label',
			'description' => 'plugins.themes.NUimmersion.options.journalDescription.description',
			'options' => array(
				0 => 'plugins.themes.NUimmersion.options.journalDescription.disable',
				1 => 'plugins.themes.NUimmersion.options.journalDescription.enable'
			)
		));

		$this->addOption('journalDescriptionColour', 'colour', array(
			'label' => 'plugins.themes.NUimmersion.options.journalDescriptionColour.label',
			'description' => 'plugins.themes.NUimmersion.options.journalDescriptionColour.description',
		));

		// Additional data to the templates
		HookRegistry::register ('TemplateManager::display', array($this, 'addIssueTemplateData'));
		HookRegistry::register ('TemplateManager::display', array($this, 'addSiteWideData'));
		HookRegistry::register ('TemplateManager::display', array($this, 'homepageAnnouncements'));
		HookRegistry::register ('TemplateManager::display', array($this, 'homepageJournalDescription'));
		HookRegistry::register ('issueform::display', array($this, 'addToIssueForm'));

		// Check if CSS embedded to the HTML galley
		HookRegistry::register('TemplateManager::display', array($this, 'hasEmbeddedCSS'));

		// Additional variable for the issue form
		HookRegistry::register('issuedao::getAdditionalFieldNames', array($this, 'addIssueDAOFieldNames'));
		HookRegistry::register('issueform::initdata', array($this, 'initDataIssueFormFields'));
		HookRegistry::register('issueform::readuservars', array($this, 'readIssueFormFields'));
		HookRegistry::register('issueform::execute', array($this, 'executeIssueFormFields'));

		// Additional variable for the announcements form
		HookRegistry::register('announcementsettingsform::Constructor', array($this, 'setAnnouncementsSettings'));
	}

	/**
	 * Get the display name of this theme
	 * @return string
	 */
	public function getDisplayName() {
		return __('plugins.themes.NUimmersion.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	public function getDescription() {
		return __('plugins.themes.NUimmersion.description');
	}

	/**
	 * @param $hookname string
	 * @param $args array [
	 *      @option TemplateManager
	 *      @option string relative path to the template
	 * ]
	 * @brief Add section-specific data to the indexJournal and issue templates
	 */

	public function addIssueTemplateData($hookname, $args) {

		/* @var $request Request
		 * @var $context Context
		 * @var $templateMgr TemplateManager
		 * @var $issueDao IssueDAO
		 * @var $issue Issue
		 * @var $publishedArticleDao PublishedArticleDAO
		 * @var $sectionDao SectionDAO
		 * @var $sections array
		 * @var $section Section
		 */

		$templateMgr = $args[0];
		$template = $args[1];
		$request = $this->getRequest();

		if ($template !== 'frontend/pages/issue.tpl' && $template !== 'frontend/pages/indexJournal.tpl') return false;

		$journal = $request->getJournal();

		$issueDao = DAORegistry::getDAO('IssueDAO');

		if ($template === 'frontend/pages/indexJournal.tpl') {
			$issue = $issueDao->getCurrent($journal->getId(), true);
		} else {
			$issue = $templateMgr->get_template_vars('issue');
		}

		if (!$issue) return false;

		$publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
		$publishedArticlesBySections = $publishedArticleDao->getPublishedArticlesInSections($issue->getId(), true);

		// Section color
		$NUimmersionSectionColors = $issue->getData('NUimmersionSectionColor');
		$sectionDao = DAORegistry::getDAO('SectionDAO');
		$sections = $sectionDao->getByIssueId($issue->getId());
		$lastSectionColor = null;

		// Section description; check if this option and BrowseBySection plugin is enabled
		$sectionDescriptionSetting = $this->getOption('sectionDescriptionSetting');
		$pluginSettingsDAO = DAORegistry::getDAO('PluginSettingsDAO');
		$request = PKPApplication::getRequest();
		$context = $request->getContext();
		$contextId = $context ? $context->getId() : 0;
		$browseBySectionSettings = $pluginSettingsDAO->getPluginSettings($contextId, 'browsebysectionplugin');
		$locale = AppLocale::getLocale();

		foreach ($publishedArticlesBySections as $sectionId => $publishedArticlesBySection) {
			foreach ($sections as $section) {
				if ($section->getId() == $sectionId) {
					// Set section and its background color
					$publishedArticlesBySections[$sectionId]['section'] = $section;
					$publishedArticlesBySections[$sectionId]['sectionColor'] = $NUimmersionSectionColors[$sectionId];

					// Check if section background color is dark
					$isSectionDark = false;
					if ($NUimmersionSectionColors[$sectionId] && $this->isColourDark($NUimmersionSectionColors[$sectionId])) {
						$isSectionDark = true;
					}
					$publishedArticlesBySections[$sectionId]['isSectionDark'] = $isSectionDark;

					// Section description
					if ($sectionDescriptionSetting == 'enable' && $browseBySectionSettings['enabled'] && $section->getData('browseByDescription', $locale)) {
						$publishedArticlesBySections[$sectionId]['sectionDescription'] = $section->getData('browseByDescription', $locale);
					}

					// Need only the color of the last section that contains articles
					if ($publishedArticlesBySections[$sectionId]['articles'] && $NUimmersionSectionColors[$sectionId]) {
						$lastSectionColor = $NUimmersionSectionColors[$sectionId];
					}
				}
			}
		}

		$templateMgr->assign(array(
			'publishedArticlesBySections' => $publishedArticlesBySections,
			'lastSectionColor' => $lastSectionColor
		));
	}

	/**
	 * @param $hookname string
	 * @param $args array [
	 *      @option TemplateManager
	 *      @option string relative path to the template
	 * ]
	 * @return boolean|void
	 * @brief background color for announcements section on the journal index page
	 */
	public function homepageAnnouncements($hookname, $args) {

		$templateMgr = $args[0];
		$template = $args[1];

		if ($template !== 'frontend/pages/indexJournal.tpl') return false;

		$request = $this->getRequest();
		$journal = $request->getJournal();

		// Announcements on index journal page
		$announcementsIntro = $journal->getLocalizedSetting('announcementsIntroduction');
		$NUimmersionAnnouncementsColor = $journal->getSetting('NUimmersionAnnouncementsColor');

		$isAnnouncementDark = false;
		if ($NUimmersionAnnouncementsColor && $this->isColourDark($NUimmersionAnnouncementsColor)) {
			$isAnnouncementDark = true;
		}

		$templateMgr->assign(array(
			'announcementsIntroduction'=> $announcementsIntro,
			'isAnnouncementDark' => $isAnnouncementDark,
			'NUimmersionAnnouncementsColor' => $NUimmersionAnnouncementsColor
		));
	}

	/**
	 * @param $hookname string
	 * @param $args array [
	 *      @option TemplateManager
	 *      @option string relative path to the template
	 * ]
	 * @return void
	 * @brief Assign additional data to Smarty templates
	 */
	public function addSiteWideData($hookname, $args) {
		$templateMgr = $args[0];

		$request = $this->getRequest();
		$journal = $request->getJournal();

		if (!defined('SESSION_DISABLE_INIT')) {

			// Check locales
			if ($journal) {
				$locales = $journal->getSupportedLocaleNames();
			} else {
				$locales = $request->getSite()->getSupportedLocaleNames();
			}

			// Load login form
			$loginUrl = $request->url(null, 'login', 'signIn');
			if (Config::getVar('security', 'force_login_ssl')) {
				$loginUrl = PKPString::regexp_replace('/^http:/', 'https:', $loginUrl);
			}

			$orcidImageUrl = $this->getPluginPath() . '/templates/images/orcid.png';

			if ($request->getContext()) {
				$templateMgr->assign('NUimmersionHomepageImage', $journal->getLocalizedSetting('homepageImage'));
			}

			$templateMgr->assign(array(
				'languageToggleLocales' => $locales,
				'loginUrl' => $loginUrl,
				'orcidImageUrl' => $orcidImageUrl
			));
		}
	}

	/**
	 * @param $hookname string
	 * @param $args array [
	 *      @option TemplateManager
	 *      @option string relative path to the template
	 * ]
	 * @return boolean|void
	 * @brief Show Journal Description on the journal landing page depending on theme settings
	 */
	public function homepageJournalDescription($hookName, $args) {
		$templateMgr = $args[0];
		$template = $args[1];

		if ($template != "frontend/pages/indexJournal.tpl") return false;

		$journalDescriptionColour = $this->getOption('journalDescriptionColour');
		$isJournalDescriptionDark = false;
		if ($journalDescriptionColour && $this->isColourDark($journalDescriptionColour)) {
			$isJournalDescriptionDark = true;
		}

		$templateMgr->assign(array(
			'showJournalDescription' => $this->getOption('journalDescription'),
			'journalDescriptionColour' => $journalDescriptionColour,
			'isJournalDescriptionDark' => $isJournalDescriptionDark
		));
	}

	/**
	 * Add section settings to IssueDAO
	 *
	 * @param $hookName string
	 * @param $args array [
	 *		@option IssueDAO
	 *		@option array List of additional fields
	 * ]
	 */
	public function addIssueDAOFieldNames($hookName, $args) {
		$fields =& $args[1];
		$fields[] = 'NUimmersionSectionColor';
	}


	/**
	 * Initialize data when form is first loaded
	 *
	 * @param $hookName string `issueform::initData`
	 * @parram $args array [
	 *		@option IssueForm
	 * ]
	 */
	public function initDataIssueFormFields($hookName, $args) {
		$issueForm = $args[0];
		$issueForm->setData('NUimmersionSectionColor', $issueForm->issue->getData('NUimmersionSectionColor'));
	}

	/**$$
	 * Read user input from additional fields in the issue editing form
	 *
	 * @param $hookName string `issueform::readUserVars`
	 * @parram $args array [
	 *		@option IssueForm
	 *		@option array User vars
	 * ]
	 */
	public function readIssueFormFields($hookName, $args) {
		$issueForm =& $args[0];
		$request = $this->getRequest();

		$issueForm->setData('NUimmersionSectionColor', $request->getUserVar('NUimmersionSectionColor'));
	}

	/**
	 * Save additional fields in the issue editing form
	 *
	 * @param $hookName string `issueform::execute`
	 * @param $args array [
	 *		@option IssueForm
	 *		@option Issue
	 *		@option Request
	 * ]
	 */
	public function executeIssueFormFields($hookName, $args) {
		$issueForm = $args[0];
		$issue = $args[1];

		$issue->setData('NUimmersionSectionColor', $issueForm->getData('NUimmersionSectionColor'));

		$issueDao = DAORegistry::getDAO('IssueDAO');
		$issueDao->updateObject($issue);
	}

	/**
	 * Add variables to the issue editing form
	 *
	 * @param $hookName string `issueform::display`; see fetch()
	 * @param $args array [
	 *		@option IssueForm
	 * ]
	 */

	public function addToIssueForm($hookName, $args) {
		$issueForm = $args[0];

		// Display only if available as per IssueForm::fetch()
		if ($issueForm->issue) {
			$request = $this->getRequest();

			$sectionDao = DAORegistry::getDAO('SectionDAO');
			$sections = $sectionDao->getByIssueId($issueForm->issue->getId());

			$templateMgr = TemplateManager::getManager($request);

			$templateMgr->assign('sections', $sections);
		}
	}

	/**
	 * @param $hookName string `TemplateManager::display`
	 * @param $args array [
	 *      @option TemplateManager
	 *      @option string relative path to the template
	 *  ]
	 */
	public function hasEmbeddedCSS($hookName, $args) {
		$templateMgr = $args[0];
		$template = $args[1];
		$request = $this->getRequest();

		// Return false if not a galley page
		if ($template !== 'plugins/plugins/generic/htmlArticleGalley/generic/htmlArticleGalley:display.tpl') return false;

		$articleArrays = $templateMgr->get_template_vars('article');

		// Deafult styling for HTML galley
		$boolEmbeddedCss = false;
		foreach ($articleArrays->getGalleys() as $galley) {
			if ($galley->getFileType() === 'text/html') {
				$submissionFile = $galley->getFile();

				$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
				import('lib.pkp.classes.submission.SubmissionFile'); // Constants
				$embeddableFiles = array_merge(
					$submissionFileDao->getLatestRevisions($submissionFile->getSubmissionId(), SUBMISSION_FILE_PROOF),
					$submissionFileDao->getLatestRevisionsByAssocId(ASSOC_TYPE_SUBMISSION_FILE, $submissionFile->getFileId(), $submissionFile->getSubmissionId(), SUBMISSION_FILE_DEPENDENT)
				);

				foreach ($embeddableFiles as $embeddableFile) {
					if ($embeddableFile->getFileType() == 'text/css') {
						$boolEmbeddedCss = true;
					}
				}
			}

		}

		$templateMgr->assign(array(
			'boolEmbeddedCss' => $boolEmbeddedCss,
			'themePath' => $request->getBaseUrl() . "/" . $this->getPluginPath(),
		));
	}

	/**
	 * Add announcement settings (colorPick) to the SettingsDAO through controller
	 *
	 * @param $hookName string
	 * @param $args array [
	 *		@option AnnouncementSettingsForm
	 *		@option string "controllers/tab/settings/announcements/form/announcementSettingsForm.tpl"
	 * ]
	 */
	public function setAnnouncementsSettings($hookName, $args) {

		/* @var $announcementSettingsForm AnnouncementSettingsForm */

		$announcementSettingsForm = $args[0];
		$settings = $announcementSettingsForm->getSettings();
		$settings += ['NUimmersionAnnouncementsColor' => 'string'];
		$announcementSettingsForm->setSettings($settings);
	}

}
