<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="metabox-holder has-right-sidebar" id="poststuff">
    <div id="side-info-column" class="inner-sidebar">
        <div class="postbox">
            <h3>About <?php echo self::PLUGIN_NAME; ?></h3>
            <div class="inside">
                <center>
                    <img width="200"
                         src="<?php echo plugins_url('/images/logo.jpg', dirname(__FILE__)); ?>"
                         alt="">
                </center>
                <p>Name : <?php echo self::PLUGIN_NAME; ?></p>
                <p>Author : <a target="_blank" href="http://www.easantos.net">Easantos</a></p>
                <p>Website : <a href="http://www.easantos.net" target="_blank">www.easantos.net</a></p>
                <p>Email : <a href="mailto:emanuelsantos@easantos.net" target="_blank">emanuelsantos@easantos.net</a></p>
            </div>
        </div>
        <div class="postbox">
            <h3>About Easantos</h3>
            <div class="inside">
                <center><img src="<?php echo plugins_url('/images/common/easantos.png', dirname(__FILE__)); ?>">
                </center>
                <p><strong>Easantos</strong> provides a full range of WordPress web development services,
                    including
                    theme implementation and plugin development at competitive prices.</p>
            </div>
        </div>
        <div class="postbox">
            <h3>Our list of plugins</h3>
            <div class="inside">
                <ul>
                    <?php foreach ($this->getOurPlugins() as $plugin) { ?>
                        <li>
                            <img
                                src="<?php echo plugins_url('/images/common/' . $plugin['slug'] . '.png', dirname(__FILE__)); ?>"
                                alt="<?php echo $plugin['name']; ?>">
                            <a target="_blank"
                               href="https://wordpress.org/plugins/<?php echo $plugin['slug']; ?>/"><?php echo $plugin['name']; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>