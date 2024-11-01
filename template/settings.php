<?php if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly ?>
<div class="wrap columns-2 dd-wrap">
    <div class="icon32" id="icon-options-general"></div>
    <h2>Settings</h2>
    <?php if (isset($successMessage) && strlen($successMessage) > 0) { ?>
        <div class="updated below-h2 is-dismissible" id="message">
            <p><?php echo $successMessage; ?></p>
        </div>
    <?php } ?>
    <?php if (isset($errorMessages)) { ?>
        <div class="notice notice-warning is-dismissible" id="message">
            <?php foreach ($errorMessages as $errorMessage) { ?>
                <p><?php echo $errorMessage; ?></p>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="metabox-holder has-right-sidebar" id="poststuff">
        <?php include 'sidebar.php'; ?>
        <div id="post-body">
            <div id="post-body-content">
                <div class="stuffbox">
                    <h3><label for="link_name">Settings</label></h3>
                    <div class="inside">
                        <form method="post">
                            <?php wp_nonce_field($this->getFormNounceId()); ?>
                            <h3 class="hndle">Main settings</h3>
                            <div class="inside">
                                <p>
                                    <label>Index table name:<br>
                                        <input size="20" type="text" name="tciContentIndexString"
                                               value="<?php echo esc_attr($tciOptions['tciContentIndexString']); ?>">
                                    </label>
                                    <br/><br/>
                                    <label>
                                        <input type="checkbox" name="tciShowOnPosts"
                                               value="1" <?php echo intval($tciOptions['tciShowOnPosts']) === 1 ? 'checked' : ''; ?>>
                                        Show on posts.</label>
                                    <br/><br/>
                                    <label><input type="checkbox" name="tciShowOnPages"
                                                  value="1" <?php echo intval($tciOptions['tciShowOnPages']) === 1 ? 'checked' : ''; ?>>
                                        Show on pages.</label>
                                    <br/><br/>
                                    <label>Excluded post IDs (example: 1,78,23,71):<br>
                                        <input size="60" type="text" name="tciHideOnIds"
                                               value="<?php echo esc_attr($tciOptions['tciHideOnIds']); ?>">
                                    </label>
                                    <br/><br/>
                                    <label>Title background color code:<br>
                                        <input size="20"
                                               type="text"
                                               name="tciTitleBackgroundColor"
                                               class="color-field"
                                               value="<?php echo esc_attr(!empty($tciOptions['tciTitleBackgroundColor']) ? $tciOptions['tciTitleBackgroundColor'] : ''); ?>">
                                    </label>
                                    <br/><br/>
                                    <label>Index Background color code:<br>
                                        <input size="20"
                                               type="text"
                                               name="tciIndexBackgroundColor"
                                               class="color-field"
                                               value="<?php echo esc_attr(!empty($tciOptions['tciIndexBackgroundColor']) ? $tciOptions['tciIndexBackgroundColor'] : ''); ?>">
                                    </label>
                                </p>
                                <input type="submit" value=" Save ">
                                <input type="hidden" name="action" value="save-settings">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>