var $theContentIndexTable = jQuery('#theContentIndexTable'),
    $theContentIndexTableindicator = jQuery('#theContentIndexTableindicator');

$theContentIndexTable.find('thead')
    .click(
        function () {
            if ($theContentIndexTable.find('tbody').is(":visible")) {
                $theContentIndexTable.find('tbody').hide();
                $theContentIndexTableindicator.html('[+]');
            } else {
                $theContentIndexTable.find('tbody').show();
                $theContentIndexTableindicator.html('[-]');
            }
        }
    );

