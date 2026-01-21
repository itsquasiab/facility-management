// Load Quill
var quill = new Quill('#editor', {
  theme: 'snow',
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline', 'strike'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link'],
      ['clean']
    ]
  }
});

// Lấy input hidden
var hiddenInput = document.querySelector('input[name="mo_ta"]');

// Khi submit form -> lấy HTML của editor
document.querySelector('form').addEventListener('submit', function () {
  hiddenInput.value = quill.root.innerHTML;
});

// Khi load trang -> đổ nội dung cũ vào editor
if (hiddenInput.value.trim() !== "") {
  quill.root.innerHTML = hiddenInput.value;
}