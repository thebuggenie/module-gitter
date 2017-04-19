<?php

    namespace thebuggenie\modules\gitter\controllers;

    use thebuggenie\core\entities\Project;
    use thebuggenie\core\framework;

    /**
     * Main controller for the gitter module
     */
    class Main extends framework\Action
    {

        /**
         * @return \thebuggenie\modules\gitter\Gitter
         * @throws \Exception
         */
        protected function _getModule()
        {
            return framework\Context::getModule('gitter');
        }

        public function runConfigureProjectSettings(framework\Request $request)
        {
            $this->forward403unless($request->isPost());
            $project_key = $request['project_key'];
            $project = Project::getByKey($project_key);

            if ($project instanceof Project && $this->getUser()->canManageProject($project))
            {
                $project_id = $project->getID();
                $module = $this->_getModule();
                $url = $request[\thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_WEBHOOK_URL];

                if (!$url) {
                    $this->_getModule()->deleteSetting(\thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_WEBHOOK_URL . $project_id);
                } else {
                    $pieces = parse_url($url);
                    if (!isset($pieces['scheme']) || !isset($pieces['path']) || !isset($pieces['host']) || $pieces['host'] = !'webhooks.gitter.im') {
                        $this->getResponse()->setHttpStatus(400);
                        return $this->renderJSON(['error' => $this->getI18n()->__("Sorry, that did not make sense"), 'webhook_url' => $pieces]);
                    }

                    $module->setProjectWebhookUrl($project_id, $url);
                    $module->setProjectIntegrationEnabled($project_id, $request[\thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_INTEGRATION_ENABLED]);
                    $module->doesPostOnNewIssues($project_id, $request[\thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_POST_ON_NEW_ISSUES]);
                    $module->doesPostOnNewComments($project_id, $request[\thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_POST_ON_NEW_COMMENTS]);
                }

                return $this->renderJSON(array('failed' => false, 'message' => framework\Context::getI18n()->__('Settings saved')));
            }
            else
            {
                $this->forward403();
            }
        }

    }

