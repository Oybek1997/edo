<template>
  <div>
    <editor
      @input="handleInput"
      api-key="no-api-key"
      :init="{
        height: 200,
        menubar: true,
        plugins: [
          'advlist autolink lists link image charmap print preview hr anchor pagebreak',
          'searchreplace wordcount visualblocks visualchars code fullscreen',
          'insertdatetime media nonbreaking save table contextmenu directionality',
          'template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample',
        ],
        browser_spellcheck: true,
        spellchecker_languages: 'Russian=ru_RU',
        spellchecker_language: 'ru_RU',
        language: 'ru',
        //forced_root_block: false,
        // force_br_newlines: true,
        force_p_newlines: true,
        // convert_newlines_to_brs: true,
        // nonbreaking_force_tab: true,
        setup: function (editor) {
          editor.addButton('indentText', {
            icon: 'indent',
            onclick: function () {
              //editor.insertContent('fkjgfkj');
              //editor.execCommand('mceInsertContent', false, 'your content');
              editor.dom.addClass(editor.selection.getNode(), 'indent');
            },
          });
          editor.addButton('indentRemove', {
            icon: 'outdent',
            onclick: function () {
              //editor.insertContent('fkjgfkj');
              //editor.execCommand('mceInsertContent', false, 'your content');
              editor.dom.removeClass(editor.selection.getNode(), 'indent');
            },
          });
          editor.on("KeyUp", (e) => {
            this.submitNewContent();
          });
        },
        content_style:
          'body { font-family:Times,sans-serif; font-size:14pt } p{margin:0px; padding:0px;} .indent{ text-indent:40px;}',
        fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt',
        toolbar:
          'fullscreen | preview | undo | redo | fontsizeselect | bold italic  strikethrough  forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist indentText indentRemove  | removeformat',
      }"
      :value="value"
    />
  </div>
</template>

<script>
// plugins: 'lineheight',
// toolbar: 'lineheightselect'
import Editor from "@tinymce/tinymce-vue";

export default {
  name: "tinymce",
  components: {
    editor: Editor,
  },
  props: {
    value: { default: "" },
  },
  data() {
    return {
      content: this.value,
    };
  },
  methods: {
    handleInput(e) {
      this.$emit("input", this.content);
      alert(this.content);
    },
  },
};
</script>
