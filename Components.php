<?php

    namespace thebuggenie\modules\gitter;

    use thebuggenie\core\framework;

    /**
     * actions for the slack module
     */
    class Components extends framework\ActionComponent
    {

        /**
         * @return \thebuggenie\modules\slack\Slack
         * @throws \Exception
         */
        protected function _getModule()
        {
            return framework\Context::getModule('gitter');
        }

        public function componentProjectconfig_panel()
        {
            $this->integration_enabled = $this->module->isProjectIntegrationEnabled($this->project->getID());
        }

    }

