<?php

namespace TheContentIndex;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use TheContentIndex\enums\EnumActions;
use TheContentIndex\enums\EnumOptions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ContentIndex
 * @package TheContentIndex
 */
class ContentIndex
{
    const PLUGIN_NAME = 'The Content Index';
    const PLUGIN_SLUG = 'the-content-index';
    const PLUGIN_DIRECTORY = '/the-content-index';
    const NOUNCE_EXPIRATION_TIME = 3600;

    private $allowedPostTypes = array(
        'post',
        'page',
        'ef_article'
    );

    /**
     * @param mixed $formNounce
     */
    public function setFormNounce($formNounce)
    {
        $this->formNounce = $formNounce;
    }

    /**
     * @return array
     */
    private function getOurPlugins()
    {
        return array(
            array(
                'slug' => 'the-content-index',
                'name' => 'The Content Index',
            ),
            array(
                'slug' => 'import-social-statistics',
                'name' => 'Import Social Statistics',
            )
        );
    }

    public function addMenuItems()
    {
        add_menu_page(
            'The Content Index',
            'The Content Index',
            10,
            'tciMain',
            'tciMain',
            plugins_url('images/icon.png', dirname(__FILE__))
        );

        add_submenu_page(
            'tciMain',
            'Settings',
            'Settings',
            1,
            'tciSettings',
            'tciSettings'
        );
    }

    public function main()
    {
        require_once WP_PLUGIN_DIR . self::PLUGIN_DIRECTORY . '/template/main.php';
    }

    /**
     * @return string
     */
    public function getFormNounceId()
    {
        return self::PLUGIN_SLUG . '-nounce';
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function validatePostBoolean($fieldName)
    {
        return !isset($_POST[$fieldName]) || $_POST[$fieldName] == '1';
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function validatePostArrayOfIds($fieldName)
    {
        return preg_match('/^[,0-9]*$/i', $_POST[$fieldName]) === 1;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function validatePostString($fieldName)
    {
        return is_string($_POST[$fieldName]);
    }

    public function validatePostSettings()
    {
        $errors = array();

        if (!$this->validatePostBoolean('tciShowOnPosts')) {
            $errors[] = 'tciShowOnPosts field must be boolean.';
        }

        if (!$this->validatePostBoolean('tciShowOnPages')) {
            $errors[] = 'tciShowOnPages field must be boolean.';
        }

        if (!$this->validatePostArrayOfIds('tciHideOnIds')) {
            $errors[] = 'tciHideOnIds field must array of integer separated by a comma.';
        }

        if (!$this->validatePostString('tciContentIndexString')) {
            $errors[] = 'tciContentIndexString field must be a generic string.';
        }

        return $errors;
    }

    public function settings()
    {
        /** @var array $tciOptions */
        $tciOptions = get_option('tciOptions');

        /** @var string $retrievedNonce */
        $retrievedNonce = $_REQUEST['_wpnonce'];

        // Check actions
        if (isset($_POST['action']) && wp_verify_nonce($retrievedNonce, $this->getFormNounceId())) {
            switch ($_POST['action']) {
                case EnumActions::SAVE_SETTINGS:
                    if (count($this->validatePostSettings())) {
                        $errorMessages = $this->validatePostSettings();

                        break;
                    }

                    if (!empty($_POST[EnumOptions::SHOW_ON_POSTS])) {
                        $tciOptions[EnumOptions::SHOW_ON_POSTS] = intval($_POST[EnumOptions::SHOW_ON_POSTS]);
                    }

                    if (!empty($tciOptions[EnumOptions::SHOW_ON_PAGES])) {
                        $tciOptions[EnumOptions::SHOW_ON_PAGES] = intval($_POST[EnumOptions::SHOW_ON_PAGES]);
                    }

                    if (!empty($tciOptions[EnumOptions::HIDE_ON_IDS])) {
                        $tciOptions[EnumOptions::HIDE_ON_IDS] = sanitize_text_field($_POST[EnumOptions::HIDE_ON_IDS]);
                    }

                    if (!empty($_POST[EnumOptions::CONTENT_INDEX_STRING])) {
                        $tciOptions[EnumOptions::CONTENT_INDEX_STRING] = sanitize_text_field($_POST[EnumOptions::CONTENT_INDEX_STRING]);
                    }

                    if (!empty($_POST[EnumOptions::TITLE_BACKGROUND_COLOR])) {
                        $tciOptions[EnumOptions::TITLE_BACKGROUND_COLOR] = sanitize_text_field($_POST[EnumOptions::TITLE_BACKGROUND_COLOR]);
                    }

                    if (!empty($_POST[EnumOptions::INDEX_BACKGROUND_COLOR])) {
                        $tciOptions[EnumOptions::INDEX_BACKGROUND_COLOR] = sanitize_text_field($_POST[EnumOptions::INDEX_BACKGROUND_COLOR]);
                    }

                    update_option(
                        'tciOptions',
                        $tciOptions
                    );

                    $successMessage = 'Settings saved. ';

                    break;
            }
        }

        set_transient(
            $this->getFormNounceId(),
            time(),
            self::NOUNCE_EXPIRATION_TIME
        );

        require_once WP_PLUGIN_DIR . self::PLUGIN_DIRECTORY . '/template/settings.php';
    }

    /**
     * @param string $content
     * @return mixed
     */
    public function content($content)
    {
        if (!in_array(get_post_type(), $this->allowedPostTypes)) {
            return $content;
        }

        if (strpos($content, '<h') === false) {
            return $content;
        }

        /** @var array $tciOptions */
        $tciOptions = get_option('tciOptions');

        if (!$tciOptions[EnumOptions::SHOW_ON_POSTS] && is_single()) {
            return $content;
        }

        if (!$tciOptions[EnumOptions::SHOW_ON_PAGES] && is_page()) {
            return $content;
        }

        /** @var array $tciHideOnIds */
        $tciHideOnIds = explode(',', $tciOptions[EnumOptions::HIDE_ON_IDS]);

        if (in_array(get_the_ID(), $tciHideOnIds)) {
            return $content;
        }

        $dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_use_internal_errors($internalErrors);
        $xp = new DOMXPath($dom);

        $expression = '
(
    //h1
    |//h2
    |//h3
    |//h4
    |//h5
    |//h6
)';

        /** @var DOMNodeList $elements */
        $elements = $xp->query($expression);
        $htmls = [];

        if (count($elements) == 0) {
            return $content;
        }

        /** @var boolean $parentElement */
        $ulOpen = false;

        /** @var DOMElement $lastElement */
        $lastElement = null;

        if ($elements->length > 0) {
            wp_enqueue_script('tciMain');
        }

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            if ($lastElement !== null && $lastElement->tagName != $element->tagName) {
                if ($ulOpen === true) {
                    $htmls [] = '</ul>';
                    $ulOpen = false;
                } elseif ($ulOpen === false) {
                    $htmls [] = '<ul>';
                    $ulOpen = true;
                }
            }

            $element->nodeValue = utf8_decode($element->nodeValue);

            $htmls [] = '<li><a href="#' . $this->basename($element->nodeValue) . '">' . $element->nodeValue . '</a></li>';

            $content = str_replace(
                '<' . $element->tagName . '>' . $element->nodeValue . '</' . $element->tagName . '>',
                '<' . $element->tagName . ' id="' . $this->basename($element->nodeValue) . '">' . $element->nodeValue . '</' . $element->tagName . '>',
                $content,
                $count
            );

            $lastElement = $element;
        }

        /** @var string $cssRaw */
        $cssRaw = file_get_contents(WP_PLUGIN_DIR . self::PLUGIN_DIRECTORY . '/public/css/main.css');
        $cssRaw = '<style>' . $cssRaw . '</style>';

        $htmlTableRaw = file_get_contents(WP_PLUGIN_DIR . self::PLUGIN_DIRECTORY . '/template/parts/table.html');
        $htmlTable = sprintf(
            $htmlTableRaw,
            'background-color: ' . $tciOptions[EnumOptions::TITLE_BACKGROUND_COLOR],
            $tciOptions['tciContentIndexString'],
            'background-color: ' . $tciOptions[EnumOptions::INDEX_BACKGROUND_COLOR],
            implode('', $htmls)
        );

        $content = str_replace(
            '<' . $elements[0]->tagName . ' id="' . $this->basename($elements[0]->nodeValue) . '">' . $elements[0]->nodeValue . '</' . $elements[0]->tagName . '>',
            $htmlTable . '<' . $elements[0]->tagName . ' id="' . $this->basename($elements[0]->nodeValue) . '">' . $elements[0]->nodeValue . '</' . $elements[0]->tagName . '>',
            $content
        );

        $content .= $cssRaw;

        return $content;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $firstH2Id
     * @return mixed
     */
    public function insertContentIndexInContent($to, $subject, $firstH2Id)
    {
        /** @var string $pattern */
        $pattern = '/<h2 id="' . $this->basename($firstH2Id) . '">(.*?)<\/h2>/';

        return preg_replace(
            $pattern,
            $to,
            $subject,
            1
        );
    }

    /**
     * @param string $string
     * @param string $separator
     * @return mixed|string
     */
    public function basename($string, $separator = '-')
    {
        $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $special_cases = array('&' => 'and', "'" => '');
        $string = mb_strtolower(trim($string), 'UTF-8');
        $string = str_replace(array_keys($special_cases), array_values($special_cases), $string);
        $string = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
        $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
        $string = preg_replace("/[$separator]+/u", "$separator", $string);

        return $string;
    }

    public function setDefaultSettings()
    {
        $tciOptions = get_option('tciOptions', [
            EnumOptions::SHOW_ON_POSTS => 1,
            EnumOptions::SHOW_ON_POSTS => 1,
            EnumOptions::HIDE_ON_IDS => '',
            EnumOptions::CONTENT_INDEX_STRING => 'Content Index',
        ]);

        update_option('tciOptions', $tciOptions);
    }
}
