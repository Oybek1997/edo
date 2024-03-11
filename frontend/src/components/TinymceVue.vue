<template>
  <div>
    <v-textarea :id="id">{{ content }}</v-textarea>
  </div>
</template>

<script>
// Import TinyMCE
import tinymce from "tinymce/tinymce";
import store from '../store';
// A theme is also required
import "tinymce/themes/modern/theme";

// Any plugins you want to use has to be imported
import "tinymce/plugins/advlist";
import "tinymce/plugins/wordcount";
import "tinymce/plugins/autolink";
import "tinymce/plugins/autosave";
import "tinymce/plugins/charmap";
import "tinymce/plugins/codesample";
import "tinymce/plugins/contextmenu";
import "tinymce/plugins/emoticons";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/hr";
import "tinymce/plugins/imagetools";
import "tinymce/plugins/insertdatetime";
import "tinymce/plugins/link";
import "tinymce/plugins/media";
import "tinymce/plugins/noneditable";
import "tinymce/plugins/paste";
import "tinymce/plugins/print";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/tabfocus";
import "tinymce/plugins/template";
import "tinymce/plugins/textpattern";
import "tinymce/plugins/visualblocks";
import "tinymce/plugins/anchor";
import "tinymce/plugins/autoresize";
import "tinymce/plugins/bbcode";
import "tinymce/plugins/code";
import "tinymce/plugins/colorpicker";
import "tinymce/plugins/directionality";
import "tinymce/plugins/fullpage";
import "tinymce/plugins/help";
import "tinymce/plugins/image";
import "tinymce/plugins/importcss";
import "tinymce/plugins/legacyoutput";
import "tinymce/plugins/lists";
import "tinymce/plugins/nonbreaking";
import "tinymce/plugins/pagebreak";
import "tinymce/plugins/preview";
import "tinymce/plugins/save";
import "tinymce/plugins/spellchecker";
import "tinymce/plugins/table";
import "tinymce/plugins/textcolor";
import "tinymce/plugins/toc";
import "tinymce/plugins/visualchars";

import "tinymce/skins/lightgray/skin.min.css";

export default {
  name: "tinymce",
  props: {
    id: {
      type: String,
      required: true,
    },
    htmlClass: { default: "", type: String },
    value: { default: "" },
    plugins: {
      default: function () {
        return [
          "advlist autolink lists link image charmap print preview hr anchor pagebreak",
          "searchreplace wordcount visualblocks visualchars code fullscreen",
          "insertdatetime media nonbreaking save table contextmenu directionality",
          "template paste textcolor colorpicker textpattern imagetools toc help emoticons hr codesample",
        ];
      },
      type: Array,
    },
    toolbar1: {
      default:
        "fullscreen | preview | undo | redo | fontsizeselect | bold italic  strikethrough  forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat",
      type: String,
    },
    toolbar2: { default: "", type: String },
    other_options: {
      default: function () {
        return {};
      },
      type: Object,
    },
    readonly: { default: false, type: Boolean },
  },
  data() {
    return {
      content: "",
      editor: null,
      cTinyMce: null,
      checkerTimeout: null,
      isTyping: false,
    };
  },
  mounted() {
    this.content = this.value;
    this.init();
  },
  beforeDestroy() {
    this.editor.destroy();
  },
  watch: {
    value: function (newValue) {
      if (!this.isTyping) {
        if (this.editor !== null) this.editor.setContent(newValue);
        else this.content = newValue;
      }
    },
    readonly(value) {
      if (value) {
        this.editor.setMode("readonly");
      } else {
        this.editor.setMode("design");
      }
    },
  },
  methods: {
    init() {
      let options = {
        selector: "#" + this.id,
        skin: false,
        toolbar1: this.toolbar1,
        toolbar2: this.toolbar2,
        plugins: this.plugins,
        init_instance_callback: this.initEditor,
        formats: {
          // Changes the default format for the bold button to produce a strong with data-style attribute
          // bold: { inline: "b", attributes: { "data-style": "bold" } },
        },
        content_style: 'body { font-size: 14pt; }',
        fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt",
        paste_preprocess: function (plugin, args) {
          // console.log(args.content);
          // replace copied text with empty string
          // if (args.content.indexOf("<table") != -1 && store.state.user.id != 1 && store.state.user.id != 424)
          // {
          //   args.content = "";
          //   alert("Вставка таблиц из других источников запрещена.");
          // }
          // else
          // args.content = args.content.replace(/<\/?[^>]+(>|$)/g, " ");
        },
        browser_spellcheck: true,
        spellchecker_languages: "Russian=ru_RU",
        spellchecker_language: "ru_RU",
        language: "ru",
        // forced_root_block: false,
        // force_br_newlines: true,
        // force_p_newlines: true,
        // convert_newlines_to_brs: true,
        // nonbreaking_force_tab: true,
      };
      tinymce.init(this.concatAssciativeArrays(options, this.other_options));
    },
    initEditor(editor) {
      this.editor = editor;
      editor.on("KeyUp", (e) => {
        this.submitNewContent();
      });
      editor.on("KeyDown", (e) => {
        // console.log(e.keyCode);
        // if (e.keyCode == 9) {
        //   e.preventDefault();
        //   editor.insertContent(
        //     "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
        //   );
        // }
      });
      editor.on("Change", (e) => {
        if (this.editor.getContent() !== this.value) {
          this.submitNewContent();
        }
        this.$emit("editorChange", e);
      });
      editor.on("init", (e) => {
        editor.setContent(this.content);
        this.$emit("input", this.content);
      });
      if (this.readonly) {
        this.editor.setMode("readonly");
      } else {
        this.editor.setMode("design");
      }

      this.$emit("editorInit", editor);
    },
    concatAssciativeArrays(array1, array2) {
      if (array2.length === 0) return array1;
      if (array1.length === 0) return array2;
      let dest = [];
      for (let key in array1) dest[key] = array1[key];
      for (let key in array2) dest[key] = array2[key];
      return dest;
    },
    submitNewContent() {
      this.isTyping = true;
      if (this.checkerTimeout !== null) clearTimeout(this.checkerTimeout);
      this.checkerTimeout = setTimeout(() => {
        this.isTyping = false;
      }, 300);
      this.$emit("input", this.editor.getContent());
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-indent: 30px;
}
</style>
