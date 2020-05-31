import atkPlugin from './atk.plugin';
import uploadService from "../services/upload.service";

export default class fileUpload extends atkPlugin {

  main() {
    this.textInput = this.$el.find('input[type="text"]');
    this.hiddenInput = this.$el.find('input[type="hidden"]');

    this.fileInput = this.$el.find('input[type="file"]');
    this.action = this.$el.find('#' + this.settings.action);
    this.actionContent = this.action.html();

    this.bar = this.$el.find('.progress');
    this.setEventHandler();
    this.setInitialState();

  }

  /**
   * Setup field initial state.
   */
  setInitialState() {
    // Set progress bar.
    this.bar.progress({
      text : {
        percent: '{percent}%',
        active: '{percent}%',
      }
    }).hide();

    this.$el.data().fileId = this.settings.file.id;
    this.hiddenInput.val(this.settings.file.id);
    this.textInput.val(this.settings.file.name);
    this.textInput.data('isTouch', false);
    if (this.settings.file.id) {
      this.setState('delete');
    }
  }

  /**
   * Update input value.
   *
   * @param fileId
   * @param fileName
   */
  updateField(fileId, fileName) {
    this.$el.data().fileId = fileId;
    this.hiddenInput.val(fileId);

    if (fileName === '' || typeof fileName === 'undefined' || fileName === null) {
      this.textInput.val(fileId);
    } else {
      this.textInput.val(fileName);
    }
  }

  /**
   * Add event handler to input element.
   */
  setEventHandler() {
    const that = this;

    this.textInput.on('click', function(e) {
      if (!e.target.value) {
        that.fileInput.click();
      }
    });

    // add event handler to action button.
    this.action.on('click', function(e) {
      if (!that.textInput.val()) {
        that.fileInput.click();
      } else {
        // When upload is complete a js action can be send to set an id
        // to the uploaded file via the jQuery data property.
        // Check if that id exist and send it with
        // delete callback, If not, default to file name.
        let id = that.$el.data().fileId;
        if (id === '' || typeof id === 'undefined' || id === null) {
          id = that.textInput.val();
        }
        that.doFileDelete(id);
      }
    });

    // add event handler to file input.
    this.fileInput.on('change', function(e) {
      if (e.target.files.length > 0) {
        that.textInput.val( e.target.files[0].name);
        //that.doFileUpload(e.target.files[0]);
        that.doFileUpload(e.target.files);
      }
    });
  }

  /**
   * Set the action button html content.
   * Set the input text content.
   */
  setState(mode) {
    const that = this;

    switch (mode) {
      case 'delete':
        this.action.html(this.getEraseContent);
        setTimeout(function() {
          that.bar.progress('reset');
          that.bar.hide('fade');
        }, 1000);
        break;
      case 'upload':
        this.action.html(this.actionContent);
        this.textInput.val('');
        this.fileInput.val('');
        this.hiddenInput.val('');
        this.$el.data().fileId = null;
        break;
    }
  }

  /**
   * Do the actual file uploading process.
   *
   * @param file the FileList object.
   */
  doFileUpload(file) {

    const that = this;
    // if submit button id is set, then disable submit
    // during upload.
    if (this.settings.submit) {
      $('#'+this.settings.submit).addClass('disabled');
    }

    // setup task on upload completion.
    let completeCb =  function(response, content) {
      if (response.success) {
        that.bar.progress('set label', that.settings.completeLabel);
        that.setState('delete');
      }

      if (that.settings.submit) {
        $('#'+that.settings.submit).removeClass('disabled');
      }
    };

    // setup progress bar update via xhr.
    let xhrCb = function() {
      let xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          let percentComplete = evt.loaded / evt.total;
          that.bar.progress('set percent', parseInt(percentComplete * 100));
        }
      }, false);
      return xhr;
    };

    that.bar.show();
    uploadService.uploadFiles(
      file,
      this.$el,
      {action: 'upload'},
      this.settings.uri,
      completeCb,
      xhrCb
    );
  }

  /**
   * Callback server for file delete.
   *
   * @param fileName
   */
  doFileDelete(fileName) {

    const that = this;

    this.$el.api({
      on: 'now',
      url: this.settings.uri,
      data: {'action': 'delete', 'f_name': fileName},
      method: 'POST',
      obj: this.$el,
      onComplete: function(response, content) {
        if (response.success) {
          that.setState('upload');
        }
      }
    });
  }

  /**
   * Return the html content for erase action button.
   *
   * @returns {string}
   */
  getEraseContent() {
    return `<i class="red remove icon" style=""></i>`;
  }
}


fileUpload.DEFAULTS = {
  uri: null,
  file: {id: null, name: null},
  uri_options: {},
  action: null,
  completeLabel: '100%',
  submit: null
};
