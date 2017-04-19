<style>
    #tab_gitter_pane .address-container:before {
        background-image: url('<?= image_url('cfg_icon_gitter.png', false, 'gitter'); ?>');
    }
</style>
<div id="tab_gitter_pane"<?php if ($selected_tab != 'gitter'): ?> style="display: none;"<?php endif; ?>>
    <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div class="rounded_box red" style="margin-top: 10px;">
            <?= __('You do not have the relevant permissions to access these settings'); ?>
        </div>
    <?php else: ?>
    <form action="<?= make_url('configure_gitter_project_settings', array('project_key' => $project->getKey())); ?>" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_gitter_project_settings', array('project_key' => $project->getKey())); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?= make_url('configure_gitter_project_settings', array('project_key' => $project->getKey())); ?>', 'gitter_form');return false;" id="gitter_form">
            <div class="project_save_container">
                <span id="gitter_form_indicator" style="display: none;"><?= image_tag('spinning_20.gif'); ?></span>
                <input class="button button-silver" type="submit" id="gitter_form_button" value="<?= __('Save settings'); ?>">
            </div>
            <div class="address-settings">
                <table class="padded_table" cellpadding=0 cellspacing=0>
                    <tr>
                        <td><label for="gitter_enable_integration"><?= __('Enable integration'); ?></label></td>
                        <td>
                            <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                                <select name="<?= \thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_INTEGRATION_ENABLED; ?>" id="gitter_enable_integration" style="width: 70px;">
                                    <option value=1<?php if ($integration_enabled): ?> selected<?php endif; ?>><?= __('Yes'); ?></option>
                                    <option value=0<?php if (!$integration_enabled): ?> selected<?php endif; ?>><?= __('No'); ?></option>
                                </select>
                            <?php else: ?>
                                <?= ($integration_enabled) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><label for="gitter_webhook_url"><?= __('Webhook URL'); ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="address-container">
                                <input type="text" value="<?= $module->getProjectWebhookUrl($project->getID()); ?>" name="<?= \thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_WEBHOOK_URL; ?>" id="gitter_webhook_url" placeholder="https://webhooks.gitter.im/e/[...]">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="config_explanation" colspan="2"><?= __('All messages will be posted to the channel set up for this webhook'); ?></td>
                    </tr>
                    <tr>
                        <td><label for="gitter_project_post_on_new_issues"><?= __('Post on new issues'); ?></label></td>
                        <td>
                            <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                                <select name="<?= \thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_POST_ON_NEW_ISSUES; ?>" id="gitter_project_post_on_new_issues" style="width: 70px;">
                                    <option value=1<?php if ($module->doesPostOnNewIssues($project->getID())): ?> selected<?php endif; ?>><?= __('Yes'); ?></option>
                                    <option value=0<?php if (!$module->doesPostOnNewIssues($project->getID())): ?> selected<?php endif; ?>><?= __('No'); ?></option>
                                </select>
                            <?php else: ?>
                                <?= ($module->doesPostOnNewIssues($project->getID())) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="gitter_project_post_on_new_comments"><?= __('Post on new comments'); ?></label></td>
                        <td>
                            <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                                <select name="<?= \thebuggenie\modules\gitter\Gitter::SETTING_PROJECT_POST_ON_NEW_COMMENTS; ?>" id="gitter_project_post_on_new_comments" style="width: 70px;">
                                    <option value=1<?php if ($module->doesPostOnNewComments($project->getID())): ?> selected<?php endif; ?>><?= __('Yes'); ?></option>
                                    <option value=0<?php if (!$module->doesPostOnNewComments($project->getID())): ?> selected<?php endif; ?>><?= __('No'); ?></option>
                                </select>
                            <?php else: ?>
                                <?= ($module->doesPostOnNewComments($project->getID())) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    <?php endif; ?>
</div>
