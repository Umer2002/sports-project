<x-spike-layouts>
  <div class="card-body border-top">
    <button type="button" class="btn mb-1 px-4 fs-4  bg-primary-subtle text-primary" data-bs-toggle="modal" data-bs-target="#samedata-modal" data-bs-whatever="@mdo">
    Add Post
  </button>

  </div>

  <!-- Modal for Form Inputs -->
  <div class="modal fade" id="samedata-modal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center">
          <h4 class="modal-title" id="exampleModalLabel1">
            New Post Form
          </h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form inside the modal -->
          <form id="postForm">
            <div class="mb-3">
              <label for="title" class="form-label">Title</label>
              <input type="text" class="form-control" id="title" placeholder="Title of your blog" />
            </div>
            <div class="mb-3">
              <label for="content" class="form-label">Content</label>
              <!-- Initialize CKEditor here -->
              <textarea class="form-control" id="editor" rows="15" placeholder="Write your content here..."></textarea>
            </div>
            <div class="mb-3">
              <label for="formFile" class="form-label">Attach Media</label>
              <input class="form-control" type="file" id="formFile" accept="image/*,video/*">
              <div id="attachmentPreview" class="mt-3 d-none">
                <div class="attachment-preview border rounded p-3">
                  <span class="d-block text-muted small mb-2">Media preview</span>
                  <div id="attachmentPreviewContent"></div>
                  <p class="small text-muted mb-0" id="attachmentCaption">Video attachments will appear as compact players in your post.</p>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label d-block">Visibility</label>
              <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-light visibility-toggle">
                <span class="visibility-toggle-label" id="visibilityClubLabel">Club Only</span>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input" type="checkbox" role="switch" id="visibilityToggle" checked>
                </div>
                <span class="visibility-toggle-label" id="visibilityPublicLabel">Public</span>
              </div>
              <div class="form-text" id="visibilityHelp">Public posts are visible to everyone.</div>
            </div>
            <button type="submit" class="btn btn-success w-100" id="saveBlog">Publish Post</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @push('css')
  <style>
    .blog-input {
      background-color: var(--bg-secondary) !important;
    }

    /* Make the CKEditor editable area resizable */
    .ck.ck-editor__editable {
      min-height: 150px;
      /* Set a minimum height */
      max-height: 500px;
      /* Set a maximum height */
      resize: vertical;
      /* Allow both vertical and horizontal resizing */
      overflow: auto;
      /* Allow scrolling if content overflows */
    }

    /* Optional: If you want to constrain the resizing within the editor container, you can use max-width */
    .ck.ck-editor__editable {
      max-width: 100%;
    }

    .attachment-preview {
      background-color: color-mix(in srgb, var(--bg-secondary) 88%, transparent);
    }

    .attachment-preview video,
    .attachment-preview img {
      border-radius: 12px;
      width: 100%;
      max-height: 220px;
      object-fit: cover;
    }

    .visibility-toggle {
      background-color: color-mix(in srgb, var(--bg-secondary) 90%, transparent) !important;
      border-color: color-mix(in srgb, var(--bs-border-color) 75%, transparent) !important;
    }

    .visibility-toggle-label {
      font-weight: 600;
      font-size: 0.85rem;
      color: var(--muted-color);
      transition: color 0.2s ease;
    }

    .visibility-toggle-label.active {
      color: var(--bs-primary);
    }

    html[data-theme='dark'] .attachment-preview,
    [data-bs-theme='dark'] .attachment-preview {
      background-color: color-mix(in srgb, #0f172a 82%, transparent);
    }

    html[data-theme='dark'] .visibility-toggle,
    [data-bs-theme='dark'] .visibility-toggle {
      background-color: color-mix(in srgb, #1e293b 90%, transparent) !important;
      border-color: color-mix(in srgb, var(--bs-border-color) 70%, transparent) !important;
    }
  </style>
  @endpush
  @push('scripts')
  <!-- CKEditor CDN -->
  <script src="https://cdn.ckeditor.com/ckeditor5/35.0.0/classic/ckeditor.js"></script>

  <script src="https://cdn.ckeditor.com/ckfinder/3.5.1/ckfinder.js"></script>
  <script>
    // Set up CSRF token in AJAX headers
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Initialize CKEditor
    let editor;
    ClassicEditor.create(document.querySelector('#editor'), {
      ckfinder: {
        uploadUrl: 'https://play2earnsports.com/blog/ckeditor/upload', // The route for image upload
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Content-Type': 'application/json' // Set content type to JSON or multipart/form-data based on your requirement
        }
      },
      toolbar: [
        'heading', 'bold', 'italic', 'link', '|',
        'bulletedList', 'numberedList', '|',
        'imageUpload', 'blockQuote', 'undo', 'redo'
      ]
    }).then(ed => {
      editor = ed;
    }).catch(error => {
      console.error('Error initializing CKEditor:', error);
    });

    const $form = $('#postForm');
    const $submitButton = $('#saveBlog');
    const $fileInput = $('#formFile');
    const $previewWrapper = $('#attachmentPreview');
    const $previewContent = $('#attachmentPreviewContent');
    const $previewCaption = $('#attachmentCaption');
    const $visibilityToggle = $('#visibilityToggle');
    const $clubLabel = $('#visibilityClubLabel');
    const $publicLabel = $('#visibilityPublicLabel');
    const canPreviewMedia = typeof window.URL !== 'undefined' && typeof URL.createObjectURL === 'function';

    function updateVisibilityState() {
      const isPublic = $visibilityToggle.is(':checked');
      $publicLabel.toggleClass('active', isPublic);
      $clubLabel.toggleClass('active', !isPublic);
      $('#visibilityHelp').text(isPublic
        ? 'Public posts are visible to everyone.'
        : 'Club Only posts stay within your club.');
    }

    $visibilityToggle.on('change', updateVisibilityState);
    updateVisibilityState();

    let previewObjectUrl = null;
    $fileInput.on('change', function() {
      if (previewObjectUrl && canPreviewMedia) {
        URL.revokeObjectURL(previewObjectUrl);
        previewObjectUrl = null;
      }

      $previewContent.empty();

      if (!this.files || this.files.length === 0) {
        $previewWrapper.addClass('d-none');
        $previewCaption.text('Video attachments will appear as compact players in your post.');
        return;
      }

      const file = this.files[0];
      const mime = file.type || '';
      if (canPreviewMedia) {
        previewObjectUrl = URL.createObjectURL(file);
      }

      let $element;

      if (mime.startsWith('video/') && previewObjectUrl) {
        $element = $('<video />', {
          class: 'w-100',
          src: previewObjectUrl,
          controls: true,
          playsinline: true
        });
        $previewCaption.text('Video attachments play in a compact viewer within your post.');
      } else if (mime.startsWith('image/') && previewObjectUrl) {
        $element = $('<img />', {
          class: 'img-fluid',
          src: previewObjectUrl,
          alt: 'Attachment preview'
        });
        $previewCaption.text('This image will display above your post content.');
      } else {
        const label = file.name || 'Selected file';
        $element = $('<p />', {
          class: 'mb-0 small',
          text: 'Attached file: ' + label
        });
        $previewCaption.text('This file will be available to download with your post.');
      }

      $previewContent.append($element);
      $previewWrapper.removeClass('d-none');
    });

    $form.on('submit', function(event) {
      event.preventDefault();

      if (!$submitButton.length) {
        return;
      }

      $submitButton.prop('disabled', true).text('Publishing...');

      const title = $('#title').val();
      const content = editor ? editor.getData() : $('#editor').val();
      const visibility = $visibilityToggle.is(':checked') ? 1 : 0;

      const formData = new FormData();
      formData.append('title', title);
      formData.append('content', content);
      formData.append('visibility', visibility);

      const fileElement = $fileInput[0];
      if (fileElement && fileElement.files.length > 0) {
        formData.append('feature_image', fileElement.files[0]);
      }

      $.ajax({
        url: '{{ route("blogs.save") }}',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          if (response.success) {
            alert(response.message);
            window.location.href = '{{ route("blogs.index") }}';
            return;
          }

          alert('Unable to save your blog post. Please try again.');
        },
        error: function() {
          alert('There was an error saving your blog post.');
        },
        complete: function() {
          $submitButton.prop('disabled', false).text('Publish Post');
        }
      });
    });

    $('#samedata-modal').on('hidden.bs.modal', function() {
      if ($form.length) {
        $form[0].reset();
      }

      if (editor) {
        editor.setData('');
      }

      if (previewObjectUrl && canPreviewMedia) {
        URL.revokeObjectURL(previewObjectUrl);
        previewObjectUrl = null;
      }

      $previewContent.empty();
      $previewWrapper.addClass('d-none');
      $previewCaption.text('Video attachments will appear as compact players in your post.');
      updateVisibilityState();
      $submitButton.prop('disabled', false).text('Publish Post');
    });
  </script>
  @endpush

</x-spike-layouts>
