$(document).ready(function() {
    /* editor */
    var textarea = $('#datamap_map');
    var editor = ace.edit('map_editor');
    textarea.hide();
    editor.setTheme("ace/theme/crimson_editor");
    editor.getSession().setValue(textarea.val());
    editor.getSession().on('change', function () {
        textarea.val(editor.getSession().getValue());
    });
    editor.getSession().setMode("ace/mode/json");
});