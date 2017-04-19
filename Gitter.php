<?php

    namespace thebuggenie\modules\gitter;

    use thebuggenie\core\entities\Issue;
    use thebuggenie\core\entities\Milestone;
    use thebuggenie\core\entities\Project;
    use thebuggenie\core\framework,
        GuzzleHttp\Client as GuzzleHttpClient,
        GuzzleHttp\Psr7\Request as GuzzleHttpRequest;

    /**
     * Gitter module for integrating with Gitter
     *
     * @author
     * @version 1.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package gitter
     * @subpackage core
     */

    /**
     * Gitter module for integrating with Gitter
     *
     * @package gitter
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    class Gitter extends \thebuggenie\core\entities\Module
    {

        const VERSION = '1.0';

        const SETTING_PROJECT_WEBHOOK_URL = 'project_webhook_url_';
        const SETTING_PROJECT_INTEGRATION_ENABLED = 'project_integration_enabled_';
        const SETTING_PROJECT_POST_ON_NEW_ISSUES = 'project_post_to_channel_on_new_issues_';
        const SETTING_PROJECT_POST_ON_NEW_COMMENTS = 'project_post_to_channel_on_new_comments_';

        protected $_has_config_settings = false;
        protected $_name = 'gitter';
        protected $_longname = 'Gitter integration';
        protected $_description = 'Gitter description here';
        protected $_module_config_title = 'Gitter integration';
        protected $_module_config_description = 'Configure the Gitter integration';

        /**
         * Return an instance of this module
         *
         * @return Gitter
         */
        public static function getModule()
        {
            return framework\Context::getModule('gitter');
        }

        protected function _initialize()
        {
            require THEBUGGENIE_MODULES_PATH . 'gitter' . DS . 'vendor' . DS . 'autoload.php';
        }

        /**
         * @param integer $project_id
         * @return GuzzleHttpClient
         */
        protected function _getProjectClient($project_id)
        {
            $client = new GuzzleHttpClient(['base_uri' => $this->getProjectWebhookUrl($project_id)]);

            return $client;
        }

        /**
         * Listener for issue creation
         *
         * @Listener(module="core", identifier="thebuggenie\core\entities\Issue::createNew")
         * @param framework\Event $event
         */
        public function listen_issueCreate(framework\Event $event)
        {
            framework\Context::loadLibrary('common');
            $issue = $event->getSubject();
            $project_id = $issue->getProjectID();
            if ($this->isProjectIntegrationEnabled($project_id) && $this->doesPostOnNewIssues($project_id))
            {
                $issueUrl = framework\Context::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false);
                $projectUrl = framework\Context::getRouting()->generate('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false);
                $client = $this->_getProjectClient($project_id);
                $response = $client->post($this->getProjectWebhookUrl($project_id), [
                    'timeout' => 10,
                    'json' => [
                        'event_key' => 'issue_create',
                        'user' => [
                            'name' => $issue->getPostedBy()->getDisplayName(),
                            'username' => $issue->getPostedBy()->getUsername()
                        ],
                        'issue' => [
                            'id' => $issue->getId(),
                            'title' => $issue->getFormattedIssueNo(true, true),
                            'url' => $issueUrl
                        ],
                        'project' => [
                            'name' => $issue->getProject()->getName(),
                            'url' => $projectUrl
                        ]
                    ]
                ]);
            }
        }

        /**
         * Listener for the comment post
         *
         * @Listener(module="core", identifier="thebuggenie\core\entities\Comment::_postSave")
         * @param framework\Event $event
         */
        public function listen_issueComment(framework\Event $event)
        {
            framework\Context::loadLibrary('common');
            if (!$event->getParameter('issue') instanceof Issue)
                return;

            $comment = $event->getSubject();
            $issue = $event->getParameter('issue');
            $project_id = $issue->getProjectID();
            if ($this->isProjectIntegrationEnabled($project_id) && $this->doesPostOnNewComments($project_id))
            {
                $issueUrl = framework\Context::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false);
                $issueUrl .= '#comment_' . $comment->getId();
                $projectUrl = framework\Context::getRouting()->generate('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false);
                $client = $this->_getProjectClient($project_id);
                $response = $client->post($this->getProjectWebhookUrl($project_id), [
                    'timeout' => 10,
                    'json' => [
                        'event_key' => 'issue_comment',
                        'user' => [
                            'name' => $issue->getPostedBy()->getDisplayName(),
                            'username' => $issue->getPostedBy()->getUsername()
                        ],
                        'issue' => [
                            'id' => $issue->getId(),
                            'title' => $issue->getFormattedIssueNo(true, true),
                            'url' => $issueUrl
                        ],
                        'project' => [
                            'name' => $issue->getProject()->getName(),
                            'url' => $projectUrl
                        ]
                    ]
                ]);
            }
        }

        protected function _addListeners()
        {
            framework\Event::listen('core', 'thebuggenie\core\entities\Issue::createNew', array($this, 'listen_issueCreate'));
            framework\Event::listen('core', 'thebuggenie\core\entities\Comment::_postSave', array($this, 'listen_issueComment'));
            framework\Event::listen('core', 'config_project_tabs_other', array($this, 'listen_projectconfig_tab'));
            framework\Event::listen('core', 'config_project_panes', array($this, 'listen_projectconfig_panel'));
        }

        public function listen_projectconfig_tab(framework\Event $event)
        {
            include_component('gitter/projectconfig_tab', array('selected_tab' => $event->getParameter('selected_tab'), 'module' => $this));
        }

        public function listen_projectconfig_panel(framework\Event $event)
        {
            include_component('gitter/projectconfig_panel', array('selected_tab' => $event->getParameter('selected_tab'), 'access_level' => $event->getParameter('access_level'), 'project' => $event->getParameter('project'), 'module' => $this));
        }

        protected function _install($scope)
        {
        }

        protected function _loadFixtures($scope)
        {
        }

        protected function _uninstall()
        {
        }

        public function getProjectWebhookUrl($project_id)
        {
            return $this->getSetting(self::SETTING_PROJECT_WEBHOOK_URL . $project_id);
        }

        public function setProjectWebhookUrl($project_id, $value)
        {
            return $this->saveSetting(self::SETTING_PROJECT_WEBHOOK_URL . $project_id, $value);
        }

        public function isProjectIntegrationEnabled($project_id)
        {
            return (bool) $this->getSetting(self::SETTING_PROJECT_INTEGRATION_ENABLED . $project_id);
        }

        public function setProjectIntegrationEnabled($project_id, $value)
        {
            return $this->saveSetting(self::SETTING_PROJECT_INTEGRATION_ENABLED . $project_id, $value);
        }

        public function doesPostOnNewIssues($project_id, $value = null)
        {
            if ($value !== null) {
                return $this->saveSetting(self::SETTING_PROJECT_POST_ON_NEW_ISSUES . $project_id, (bool) $value);
            } else {
                $setting = $this->getSetting(self::SETTING_PROJECT_POST_ON_NEW_ISSUES . $project_id);
                return (isset($setting)) ? $setting : true;
            }
        }

        public function doesPostOnNewComments($project_id, $value = null)
        {
            if ($value !== null) {
                return $this->saveSetting(self::SETTING_PROJECT_POST_ON_NEW_COMMENTS . $project_id, (bool) $value);
            } else {
                $setting = $this->getSetting(self::SETTING_PROJECT_POST_ON_NEW_COMMENTS . $project_id);
                return (isset($setting)) ? $setting : true;
            }
        }

    }
