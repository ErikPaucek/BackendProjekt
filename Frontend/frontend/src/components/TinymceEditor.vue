<template>
  <Editor
    v-model="localValue"
    :init="tinymceConfig"
    api-key="8tszzirtg4tqdocu670ilh2klf3sgeotmljezhfywqc0tawp"
  />
</template>

<script setup>
import { ref, watch } from 'vue'
import Editor from '@tinymce/tinymce-vue'

const props = defineProps({
  modelValue: String
})
const emit = defineEmits(['update:modelValue'])

const localValue = ref(props.modelValue)
watch(() => props.modelValue, v => localValue.value = v)
watch(localValue, v => emit('update:modelValue', v))

const tinymceConfig = {
  height: 400,
  menubar: true,
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
    'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount'
  ],
  toolbar:
    'undo redo | formatselect | bold italic underline backcolor | ' +
    'alignleft aligncenter alignright alignjustify | ' +
    'bullist numlist outdent indent | removeformat | help | link image table image',
  image_title: true,
  automatic_uploads: true,
  file_picker_types: 'file image',
  paste_data_images: false,
  images_upload_url: 'http://localhost:8000/api/upload-image',
  images_upload_handler: (blobInfo) => {
    return new Promise((resolve, reject) => {
      const formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      const token = localStorage.getItem('token');
      fetch('http://localhost:8000/api/upload-image', {
        method: 'POST',
        headers: token ? { Authorization: `Bearer ${token}` } : {},
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.location) {
            resolve(data.location);
          } else {
            reject('Upload failed');
          }
        })
        .catch(() => reject('Upload error'));
    });
  },
  setup: (editor) => {
    editor.on('drop', function (e) {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) {
        const file = dt.files[0];
        const allowed = [
          'application/pdf',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
          'application/msword',
          'image/jpeg', 'image/png', 'image/gif', 'image/webp'
        ];
        if (allowed.includes(file.type)) {
          e.preventDefault();
          const formData = new FormData();
          formData.append('file', file);
          const token = localStorage.getItem('token');
          fetch('http://localhost:8000/api/upload-image', {
            method: 'POST',
            headers: token ? { Authorization: `Bearer ${token}` } : {},
            body: formData,
          })
            .then(res => res.json())
            .then(data => {
              if (data.location) {
                if (file.type.startsWith('image/')) {
                  editor.insertContent(`<img src="${data.location}" />`);
                } else {
                  editor.insertContent(`<a href="${data.location}" target="_blank" download="${file.name}">${file.name}</a>`);
                }
              }
            })
        }
      }
    });
  }
}
</script>