<?php

  function holu_attachment_escape($value){
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }

  function holu_attachment_file_size($path){
    $file_path = $path;

    if(!file_exists($file_path)){
      $file_path = __DIR__ . '/' . ltrim($path, '/');
    }

    if(!file_exists($file_path)){
      return 'Unknown size';
    }

    $bytes = filesize($file_path);

    if($bytes === false){
      return 'Unknown size';
    }

    $units = ['B', 'KB', 'MB', 'GB'];
    $size = (float) $bytes;
    $unit_index = 0;

    while($size >= 1024 && $unit_index < count($units) - 1){
      $size = $size / 1024;
      $unit_index++;
    }

    if($unit_index === 0){
      return number_format($size, 0) . ' ' . $units[$unit_index];
    }

    return number_format($size, 1) . ' ' . $units[$unit_index];
  }

  function holu_render_attachment_preview($attachment_path){
    $attachment_name = basename($attachment_path);
    $attachment_extension = strtolower(pathinfo($attachment_name, PATHINFO_EXTENSION));
    $attachment_url = holu_attachment_escape($attachment_path);
    $attachment_title = holu_attachment_escape($attachment_name);

    if($attachment_extension === 'pdf'){
      $attachment_size = holu_attachment_escape(holu_attachment_file_size($attachment_path));
      ?>
      <div class="attachment-preview-col col-sm-6 col-xl-4 filter-item all web illustrator">
        <div class="attachment-pdf-card">
          <div class="attachment-pdf-main">
            <div class="attachment-pdf-icon" aria-hidden="true">
              <i class="far fa-file-pdf"></i>
            </div>
            <div class="attachment-pdf-details">
              <h4 class="attachment-pdf-name" title="<?php echo $attachment_title; ?>"><?php echo $attachment_title; ?></h4>
              <span class="attachment-pdf-size"><?php echo $attachment_size; ?></span>
            </div>
          </div>
          <div class="attachment-pdf-actions">
            <a class="btn btn-sm attachment-pdf-action attachment-pdf-preview" href="<?php echo $attachment_url; ?>" target="_blank" rel="noopener">
              <i class="far fa-eye"></i> Preview
            </a>
            <a class="btn btn-sm attachment-pdf-action attachment-pdf-download" href="<?php echo $attachment_url; ?>" download="<?php echo $attachment_title; ?>">
              <i class="fas fa-download"></i> Download
            </a>
          </div>
        </div>
      </div>
      <?php
    }else{
      ?>
      <div class="col-sm-6 col-xl-3 filter-item all web illustrator">
        <div class="gal-box">
          <a title="Screenshot-1">
            <img src="<?php echo $attachment_url; ?>" class="img-fluid" alt="work-thumbnail">
          </a>
          <div class="gall-info">
            <h4 class="font-16 mt-0"><?php echo $attachment_title; ?></h4>
          </div> <!-- gallery info -->
        </div> <!-- end gal-box -->
      </div> <!-- end col -->
      <?php
    }
  }
