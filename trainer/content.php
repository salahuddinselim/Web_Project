<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_name = $_SESSION['full_name'];

// Get all content for this trainer
$contents = getTrainerContent($trainer_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Content Management - Pranayom</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
    body { background-color: #121712; color: white; display: flex; min-height: 100vh; }
    
    /* Layout */
    .sidebar { width: 250px; background-color: #0d110d; padding: 30px 20px; border-right: 1px solid #222; }
    .main-content { flex: 1; padding: 40px 60px; overflow-y: auto; }
    
    h1 { font-size: 32px; margin-bottom: 40px; }
    
    .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .btn-add { background-color: #00d26a; color: #003300; padding: 12px 24px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-add:hover { background-color: #00ff80; }
    
    .search-container { margin-bottom: 30px; width: 100%; max-width: 400px; }
    .search-input { width: 100%; padding: 12px; background-color: #1a201a; border: 1px solid #333; border-radius: 5px; color: white; outline: none; transition: 0.3s; }
    .search-input:focus { border-color: #00d26a; }

    .content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
    .content-card { background-color: #1a201a; border-radius: 12px; overflow: hidden; border: 1px solid #333; display: flex; flex-direction: column; transition: 0.3s; height: 100%; }
    .content-card:hover { transform: translateY(-5px); border-color: #00d26a; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    
    .card-media { height: 200px; background-color: #0d110d; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; background-image: linear-gradient(45deg, #0d110d, #1a201a); }
    .card-media img { width: 100%; height: 100%; object-fit: cover; }
    .card-media .type-badge { position: absolute; top: 15px; right: 15px; padding: 6px 12px; border-radius: 6px; font-size: 10px; font-weight: bold; background: rgba(0,0,0,0.8); color: white; text-transform: uppercase; letter-spacing: 1px; z-index: 10; }
    .type-video { color: #f24e4e !important; box-shadow: 0 0 10px rgba(242, 78, 78, 0.3); }
    .type-article { color: #00d26a !important; box-shadow: 0 0 10px rgba(0, 210, 106, 0.3); }
    
    .card-body { padding: 25px; flex: 1; display: flex; flex-direction: column; }
    .card-body h3 { font-size: 20px; margin-bottom: 12px; color: white; line-height: 1.4; }
    .card-body .tags { font-size: 13px; color: #666; margin-top: auto; display: flex; gap: 8px; flex-wrap: wrap; }
    .tag { background: #0d110d; padding: 3px 8px; border-radius: 4px; border: 1px solid #222; }
    
    .card-actions { display: flex; gap: 12px; border-top: 1px solid #222; padding: 20px 25px; background: #161c16; }
    .btn-action { flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #444; background: transparent; color: #bbb; cursor: pointer; font-size: 13px; font-weight: 500; transition: 0.3s; }
    .btn-action:hover { background: #333; color: white; border-color: #666; }
    .btn-delete:hover { border-color: #f24e4e; color: #f24e4e; background: rgba(242, 78, 78, 0.05); }

    /* Empty State */
    .empty-state { text-align: center; padding: 60px; background: #1a201a; border-radius: 12px; border: 1px dashed #333; grid-column: 1 / -1; }
    .empty-state-icon { font-size: 48px; margin-bottom: 20px; opacity: 0.5; }
    .empty-state h3 { font-size: 22px; margin-bottom: 10px; color: #ccc; }
    .empty-state p { color: #666; margin-bottom: 25px; }

    /* Modal Styling */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); align-items: center; justify-content: center; backdrop-filter: blur(5px); }
    .modal-content { background: #1a201a; width: 95%; max-width: 700px; padding: 40px; border-radius: 16px; border: 1px solid #333; box-shadow: 0 20px 50px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .modal-title { font-size: 26px; font-weight: bold; color: white; }
    .close-modal { font-size: 30px; color: #666; cursor: pointer; transition: 0.3s; line-height: 1; }
    .close-modal:hover { color: white; }
    
    .form-group { margin-bottom: 25px; }
    .form-group label { display: block; margin-bottom: 10px; font-size: 14px; color: #aaa; font-weight: 500; }
    .form-control { width: 100%; padding: 14px; background: #0d110d; border: 1px solid #333; border-radius: 8px; color: white; font-size: 15px; outline: none; transition: 0.3s; }
    .form-control:focus { border-color: #00d26a; }
    .form-textarea { height: 180px; resize: vertical; line-height: 1.6; }
    
    .type-selector { display: flex; gap: 20px; margin-bottom: 25px; }
    .type-option { flex: 1; padding: 20px; background: #0d110d; border: 1px solid #333; border-radius: 10px; cursor: pointer; text-align: center; transition: 0.3s; border: 2px solid #333; }
    .type-option:hover { border-color: #555; }
    .type-option.active { border-color: #00d26a; background: #142419; }
    .type-option .type-icon { font-size: 28px; margin-bottom: 8px; display: block; }
    .type-option .type-label { font-size: 14px; font-weight: bold; color: #ddd; }
    
    .modal-footer { display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid #222; margin-top: 30px; padding-top: 25px; }
    .btn-cancel { background: transparent; border: 1px solid #444; color: #aaa; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 500; transition: 0.3s; }
    .btn-cancel:hover { background: #222; color: white; }
    .btn-save { background: #00d26a; border: none; color: #003300; padding: 12px 35px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 15px; transition: 0.3s; }
    .btn-save:hover { background: #00ff80; transform: scale(1.02); }

    .help-text { font-size: 12px; color: #666; margin-top: 8px; display: block; }

    #videoSection, #articleSection { display: none; padding: 20px; background: #161c16; border-radius: 10px; border: 1px solid #222; margin-bottom: 25px; }

    /* Sidebar classes override if needed */
    .sidebar .menu-item.active { background-color: #1f261f; color: white; }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #0d110d; }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #444; }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

  <div class="main-content">
    <div class="content-header">
      <h1>Content Management</h1>
      <button class="btn-add" onclick="openAddModal()">+ Create New Content</button>
    </div>

    <div class="search-container">
      <input type="text" class="search-input" placeholder="Search by title or tags..." onkeyup="filterContent(this.value)">
    </div>

    <div class="content-grid" id="contentGrid">
      <?php if (empty($contents)): ?>
        <div class="empty-state" id="emptyState">
          <div class="empty-state-icon">📄</div>
          <h3>No content yet</h3>
          <p>You haven't uploaded any training materials. Start by sharing an article or a video.</p>
          <button class="btn-save" onclick="openAddModal()">Get Started</button>
        </div>
      <?php else: ?>
        <?php foreach ($contents as $c): ?>
          <div class="content-card" data-title="<?php echo htmlspecialchars(strtolower($c['title'])); ?>" data-tags="<?php echo htmlspecialchars(strtolower($c['tags'])); ?>">
            <div class="card-media">
              <span class="type-badge type-<?php echo $c['content_type']; ?>"><?php echo $c['content_type']; ?></span>
              <?php if ($c['content_type'] === 'video'): ?>
                <div style="font-size: 60px;">🎬</div>
              <?php elseif (!empty($c['thumbnail'])): ?>
                <img src="../<?php echo $c['thumbnail']; ?>" alt="Thumbnail">
              <?php else: ?>
                <div style="font-size: 60px;">📄</div>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <h3><?php echo htmlspecialchars($c['title']); ?></h3>
              <div class="tags">
                <?php 
                $tagArr = explode(',', $c['tags']);
                foreach($tagArr as $tag): 
                  if(trim($tag)):
                ?>
                  <span class="tag">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                <?php 
                  endif;
                endforeach; ?>
              </div>
            </div>
            <div class="card-actions">
              <button class="btn-action" onclick='openEditModal(<?php echo json_encode($c); ?>)'>Edit</button>
              <button class="btn-action btn-delete" onclick="deleteContent(<?php echo $c['content_id']; ?>)">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content Modal -->
  <div id="contentModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle" class="modal-title">Create New Content</h2>
        <span class="close-modal" onclick="closeModal()">&times;</span>
      </div>
      
      <form id="contentForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="content_id" id="contentId">
        
        <div class="form-group">
          <label>Content Title</label>
          <input type="text" name="title" id="contentTitle" class="form-control" placeholder="e.g. 10 Minutes Power Yoga Flow" required>
        </div>

        <div class="form-group">
          <label>Choose Content Type</label>
          <div class="type-selector">
            <div class="type-option" id="btnTypeArticle" onclick="setContentType('article')">
              <span class="type-icon">📄</span>
              <span class="type-label">Article / Guide</span>
            </div>
            <div class="type-option" id="btnTypeVideo" onclick="setContentType('video')">
              <span class="type-icon">🎬</span>
              <span class="type-label">Video Workout</span>
            </div>
          </div>
          <input type="hidden" name="content_type" id="contentType" value="article">
        </div>

        <!-- Article Section -->
        <div id="articleSection">
          <div class="form-group">
            <label>Article Body</label>
            <textarea name="content_body" id="articleBody" class="form-control form-textarea" placeholder="Share your knowledge and tips here..."></textarea>
          </div>
          <div class="form-group">
            <label>Featured Photo</label>
            <input type="file" name="photo" id="articlePhoto" class="form-control" accept="image/*">
            <span id="articlePhotoHelp" class="help-text">JPG, PNG or WEBP. This will be shown on the content card.</span>
            <small id="articlePhotoNote" style="color: #00d26a; display: block; margin-top: 5px;"></small>
          </div>
        </div>

        <!-- Video Section -->
        <div id="videoSection">
          <div class="form-group">
            <label>Upload Video File</label>
            <input type="file" name="video_file" id="videoFile" class="form-control" accept="video/*">
            <span id="videoFileHelp" class="help-text">MP4, MOV or AVI formats. Maximum 50MB recommended.</span>
            <small id="videoFileNote" style="color: #00d26a; display: block; margin-top: 5px;"></small>
          </div>
        </div>

        <div class="form-group">
          <label>Tags (Comma separated)</label>
          <input type="text" name="tags" id="contentTags" class="form-control" placeholder="e.g. yoga, strength, cardio, recovery">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn-save" id="btnSubmit">Save Content</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function setContentType(type) {
      document.getElementById('contentType').value = type;
      document.getElementById('btnTypeArticle').classList.toggle('active', type === 'article');
      document.getElementById('btnTypeVideo').classList.toggle('active', type === 'video');
      
      document.getElementById('articleSection').style.display = type === 'article' ? 'block' : 'none';
      document.getElementById('videoSection').style.display = type === 'video' ? 'block' : 'none';
      
      // Clear help text/notes if just switching
      if (type === 'article') {
          document.getElementById('videoFile').value = '';
      } else {
          document.getElementById('articlePhoto').value = '';
          document.getElementById('articleBody').value = '';
      }
    }

    function openAddModal() {
      document.getElementById('modalTitle').innerText = 'Create New Content';
      document.getElementById('formAction').value = 'add';
      document.getElementById('contentId').value = '';
      document.getElementById('btnSubmit').innerText = 'Save Content';
      document.getElementById('contentForm').reset();
      setContentType('article');
      document.getElementById('articlePhotoNote').innerText = '';
      document.getElementById('videoFileNote').innerText = '';
      document.getElementById('contentModal').style.display = 'flex';
    }

    function openEditModal(content) {
      document.getElementById('modalTitle').innerText = 'Update Content';
      document.getElementById('formAction').value = 'edit';
      document.getElementById('contentId').value = content.content_id;
      document.getElementById('contentTitle').value = content.title;
      document.getElementById('contentTags').value = content.tags;
      document.getElementById('articleBody').value = content.content_body || '';
      document.getElementById('btnSubmit').innerText = 'Update Changes';
      
      setContentType(content.content_type);
      
      if (content.content_type === 'article' && content.thumbnail) {
          document.getElementById('articlePhotoNote').innerText = '📄 Current: ' + content.thumbnail.split('/').pop();
      }
      if (content.content_type === 'video' && content.file_path) {
          document.getElementById('videoFileNote').innerText = '🎬 Current: ' + content.file_path.split('/').pop();
      }
      
      document.getElementById('contentModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('contentModal').style.display = 'none';
    }

    document.getElementById('contentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      const btn = document.getElementById('btnSubmit');
      const originalText = btn.innerText;
      btn.innerText = 'Processing...';
      btn.disabled = true;

      fetch('../handlers/trainer/manage_content.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Error: ' + data.message);
            btn.innerText = originalText;
            btn.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Check file size limits or network.');
          btn.innerText = originalText;
          btn.disabled = false;
        });
    });

    function deleteContent(id) {
      if (confirm('Are you permanently deleting this content?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('content_id', id);

        fetch('../handlers/trainer/manage_content.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              location.reload();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting.');
          });
      }
    }

    function filterContent(query) {
      query = query.toLowerCase();
      const cards = document.querySelectorAll('.content-card');
      let visibleCount = 0;
      
      cards.forEach(card => {
        const title = card.getAttribute('data-title');
        const tags = card.getAttribute('data-tags');
        if (title.includes(query) || tags.includes(query)) {
          card.style.display = 'flex';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });
      
      const emptyState = document.getElementById('emptyState');
      if (emptyState && !cards.length) {
          emptyState.style.display = 'block';
      }
    }

    window.onclick = function(event) {
      const modal = document.getElementById('contentModal');
      if (event.target == modal) {
        closeModal();
      }
    };
  </script>
</body>

</html>