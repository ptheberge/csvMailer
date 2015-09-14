<div style="width: 900px; float: left;">
<p>For the file: <span id="filename"><?php echo $data['filename']; ?></span></p>
<p>You may rename the headers:</p>
<p>(Renaming the headers will update the shortcodes below. Please do the update before writing your message or creating tables.)</p>
<?php
foreach($data['json'][0] as $key => $value) {
    echo '<input type="text" value="' . $value . '" data-attr-key="' . $key . '">';
}
?>
<br/>
<button type="button" id="save-headers">Save Headers</button>

<p>You may now generate tables.</p>
<button id="add-table">Add table</button><br/>


<p>You may now add your message...</p>
<p><input type="text" id="subject" value="Subject"></p>
<?php
foreach($data['json'][0] as $key => $value) {
    echo '<button class="header-button" type="button" data-attr-key="' . $key . '" data-attr-shortcode="{{' . $value . '}}">' . $value . '</button>';
}
?>
<p>Available tables...</p>
<div id="available-tables" style="min-height: 50px; border: 1px dotted black; max-width: 600px;"></div>
<br/>
<textarea rows="15" cols="100" id="message"></textarea>
<br/>
<button id="email-preview">Email Preview</button>
<button>Generate PDF</button>
</div>

<div style="width: 450px; float: left; height: 800px; overflow: auto;" id="preview"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $(document).on('click', '#email-preview', function () {
        generatePreview();
    });
    $(document).on('click', '.header-button', function () {
        var shortcode = $(this).attr('data-attr-shortcode');
        var cursorPosition = $('#message').prop('selectionStart');
        var currentMessage = $('#message').val();
        var textBefore = currentMessage.substring(0, cursorPosition);
        var textAfter = currentMessage.substring(cursorPosition, currentMessage.length);
        $('#message').val(textBefore + shortcode + textAfter);
    });
    $(document).on('click', '#add-table', function () {
        var columns = '8|1|3';
        $('#available-tables').append('<button class="btn-table" data-attr-columns="' + columns + '" data-attr-shortcode="{{Table_1}}">Table_1</button>');
    });
});
function generatePreview() {
    var subject = $('#subject').val();
    var message = $('#message').val();
    var filename = $('#filename').html();
    var tables = [];
    $('.btn-table').each(function () {
        var table = {};
        table.columns = $(this).attr('data-attr-columns');
        table.shortcode = $(this).attr('data-attr-shortcode');
        tables.push(table);
    });
    tables = JSON.stringify(tables);
    $('#preview').html('');
    $.post('/csv/generatePreview', {filename: filename, subject: subject, message: message, tables: tables}, function (data) {
        data = JSON.parse(data);
        data_length = data.length;
        for(var i = 0; i < data_length; i++) {
            $('#preview').append('<h4>' + data[i].recipient + '</h4><p><i>Will recieve the following message via email:</i></p><p>' + data[i].message + '</p><hr/>');
        }
    });
}
</script>