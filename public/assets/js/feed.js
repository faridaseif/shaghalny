// Social Feed JavaScript
(function () {
    'use strict';

    // DOM Elements
    const postForm = document.getElementById('post-form');
    const postContent = document.getElementById('post-content');
    const shareBtn = document.getElementById('share-btn');
    const photoInput = document.getElementById('photo-input');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeImageBtn = document.getElementById('remove-image');
    const postTypeInput = document.getElementById('post-type');
    const jobBtn = document.getElementById('job-btn');

    // Share Post (Form Submission with File Upload)
    if (postForm) {
        postForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to post.');
                return;
            }

            const content = postContent.value.trim();
            const file = photoInput.files[0];

            if (!content && !file) {
                ModalUI.alert('Please enter some content or select a photo', 'Empty Post');
                return;
            }

            shareBtn.disabled = true;
            shareBtn.textContent = 'Sharing...';

            const formData = new FormData();
            formData.append('content', content);
            if (file) {
                formData.append('photo', file);
                formData.append('post_type', 'photo');
            } else {
                formData.append('post_type', postTypeInput.value);
            }

            fetch('index.php?controller=post&action=create', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                         console.error('Server returned invalid JSON:', text);
                         throw new Error('Server returned invalid data: ' + text.substring(0, 100));
                    }
                }))
                .then(data => {
                    if (data.success) {
                        postContent.value = '';
                        photoInput.value = '';
                        imagePreview.style.display = 'none';
                        postTypeInput.value = 'text';
                        // Reload page to show new post
                        window.location.reload();
                    } else {
                        ModalUI.alert(data.error || 'Failed to share post', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ModalUI.alert('An error occurred while sharing your post', 'Error');
                })
                .finally(() => {
                    shareBtn.disabled = false;
                    shareBtn.textContent = 'Share';
                });
        });
    }

    // Photo Input Handler with Preview
    if (photoInput) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    ModalUI.alert('Please select an image file', 'Invalid File');
                    photoInput.value = '';
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    ModalUI.alert('Image size must be less than 5MB', 'File Too Large');
                    photoInput.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    postTypeInput.value = 'photo';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Remove Image
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function () {
            photoInput.value = '';
            imagePreview.style.display = 'none';
            postTypeInput.value = 'text';
        });
    }

    // Job Update Button
    if (jobBtn) {
        jobBtn.addEventListener('click', function () {
            postTypeInput.value = 'job_update';
            ModalUI.alert('Job update feature coming soon!', 'Coming Soon');
        });
    }

    // Like/Unlike Post
    document.addEventListener('click', function (e) {
        if (e.target.closest('.action-like')) {
            const likeBtn = e.target.closest('.action-like');
            const postId = likeBtn.dataset.postId;

            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to like posts.');
                return;
            }

            likeBtn.disabled = true;

            fetch('index.php?controller=post&action=toggleLike', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    post_id: postId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const likeCount = likeBtn.querySelector('.like-count');
                        likeCount.textContent = data.likes_count;

                        if (data.liked) {
                            likeBtn.classList.add('liked');
                        } else {
                            likeBtn.classList.remove('liked');
                        }
                    } else {
                        ModalUI.alert(data.error || 'Failed to like post', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ModalUI.alert('An error occurred', 'Error');
                })
                .finally(() => {
                    likeBtn.disabled = false;
                });
        }
    });

    // Toggle Comments Section
    document.addEventListener('click', function (e) {
        if (e.target.closest('.action-comment')) {
            const commentBtn = e.target.closest('.action-comment');
            const postId = commentBtn.dataset.postId;
            const commentsSection = document.getElementById('comments-' + postId);

            if (commentsSection.style.display === 'none') {
                commentsSection.style.display = 'block';
                loadComments(postId);
            } else {
                commentsSection.style.display = 'none';
            }
        }
    });

    // Load Comments
    function loadComments(postId) {
        const commentsList = document.getElementById('comments-list-' + postId);
        if (!commentsList) {
            console.error('Comments list element not found for post:', postId);
            return;
        }

        commentsList.innerHTML = '<p style="color: #999; padding: 1rem; text-align: center;">Loading comments...</p>';

        // Try to get the base path from the current page URL
        // Use explicit path
        const url = 'index.php?controller=post&action=getComments&post_id=' + encodeURIComponent(postId);
        console.log('Fetching comments from:', url);

        fetch(url)
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response');
                    });
                }

                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'HTTP error! status: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (!data.comments || data.comments.length === 0) {
                        commentsList.innerHTML = '<p style="color: #999; padding: 1rem; text-align: center;">No comments yet</p>';
                    } else {
                        commentsList.innerHTML = data.comments.map(comment => {
                            const firstLetter = comment.author_name ? comment.author_name.charAt(0).toUpperCase() : 'U';
                            const timeAgo = formatTimeAgo(comment.created_at);
                            return `
                                <div class="comment-item">
                                    <div class="comment-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        ${firstLetter}
                                    </div>
                                    <div class="comment-content">
                                        <div class="comment-author">${escapeHtml(comment.author_name || 'Unknown')} <span style="color: #999; font-size: 0.85rem;">${timeAgo}</span></div>
                                        <div class="comment-text">${escapeHtml(comment.content)}</div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }
                } else {
                    const errorMsg = data.error || 'Error loading comments';
                    commentsList.innerHTML = '<p style="color: #e74c3c; padding: 1rem;">' + escapeHtml(errorMsg) + '</p>';
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                commentsList.innerHTML = '<p style="color: #e74c3c; padding: 1rem;">Error loading comments</p>';
            });
    }

    // Add Comment
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('comment-submit')) {
            const submitBtn = e.target;
            const postId = submitBtn.dataset.postId;
            const commentInput = document.querySelector(`.comment-input[data-post-id="${postId}"]`);
            const content = commentInput.value.trim();

            if (!content) {
                ModalUI.alert('Please enter a comment', 'Empty Comment');
                return;
            }

            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to comment.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';

            fetch('index.php?controller=post&action=addComment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    post_id: postId,
                    content: content
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        commentInput.value = '';
                        // Update comment count
                        const commentBtn = document.querySelector(`.action-comment[data-post-id="${postId}"]`);
                        const commentCount = commentBtn.querySelector('.comment-count');
                        commentCount.textContent = data.comments_count;

                        // Reload comments
                        loadComments(postId);
                    } else {
                        ModalUI.alert(data.error || 'Failed to post comment', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ModalUI.alert('An error occurred while posting comment', 'Error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Post';
                });
        }
    });

    // Submit comment on Enter key
    document.addEventListener('keypress', function (e) {
        if (e.target.classList.contains('comment-input') && e.key === 'Enter') {
            const postId = e.target.dataset.postId;
            const submitBtn = document.querySelector(`.comment-submit[data-post-id="${postId}"]`);
            if (submitBtn) {
                submitBtn.click();
            }
        }
    });

    // Helper Functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTimeAgo(datetime) {
        const now = new Date();
        const then = new Date(datetime);
        const diff = Math.floor((now - then) / 1000); // seconds

        if (diff < 60) return 'just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';

        return then.toLocaleDateString();
    }

    // Post Menu (Edit/Delete)
    document.addEventListener('click', function (e) {
        // Toggle menu dropdown
        if (e.target.classList.contains('menu-btn')) {
            const postId = e.target.dataset.postId;
            const menu = document.getElementById('menu-' + postId);
            const allMenus = document.querySelectorAll('.menu-dropdown');

            // Close all other menus
            allMenus.forEach(m => {
                if (m.id !== 'menu-' + postId) {
                    m.style.display = 'none';
                }
            });

            // Toggle current menu
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Close menu when clicking outside
        if (!e.target.closest('.post-actions-menu')) {
            document.querySelectorAll('.menu-dropdown').forEach(menu => {
                menu.style.display = 'none';
            });
        }
    });

    // Edit Post
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-post')) {
            const postId = e.target.dataset.postId;
            const content = e.target.dataset.content;
            const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);
            const editForm = document.getElementById('edit-form-' + postId);
            const textarea = editForm.querySelector('.edit-textarea');

            postText.style.display = 'none';
            editForm.style.display = 'block';
            textarea.value = content;
            textarea.focus();

            // Close menu
            document.getElementById('menu-' + postId).style.display = 'none';
        }
    });

    // Cancel Edit
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('cancel-edit')) {
            const postId = e.target.dataset.postId;
            const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);
            const editForm = document.getElementById('edit-form-' + postId);

            postText.style.display = 'block';
            editForm.style.display = 'none';
        }
    });

    // Save Edit
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('save-edit')) {
            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to edit posts.');
                return;
            }

            const postId = e.target.dataset.postId;
            const editForm = document.getElementById('edit-form-' + postId);
            const textarea = editForm.querySelector('.edit-textarea');
            const content = textarea.value.trim();

            if (!content) {
                ModalUI.alert('Content cannot be empty', 'Validation Error');
                return;
            }

            e.target.disabled = true;
            e.target.textContent = 'Saving...';

            fetch('index.php?controller=post&action=update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    post_id: postId,
                    content: content
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);
                        postText.innerHTML = escapeHtml(content).replace(/\n/g, '<br>');
                        postText.style.display = 'block';
                        editForm.style.display = 'none';
                    } else {
                        ModalUI.alert(data.error || 'Failed to update post', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ModalUI.alert('An error occurred while updating post', 'Error');
                })
                .finally(() => {
                    e.target.disabled = false;
                    e.target.textContent = 'Save';
                });
        }
    });

    // Delete Post
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-post')) {
            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to delete posts.');
                return;
            }

            ModalUI.confirm('Are you sure you want to delete this post?', 'Delete Post').then(isConfirmed => {
                if (!isConfirmed) return;

                const postId = e.target.dataset.postId;
                const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);

                fetch('index.php?controller=post&action=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        post_id: postId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            postCard.style.transition = 'opacity 0.3s';
                            postCard.style.opacity = '0';
                            setTimeout(() => {
                                postCard.remove();
                            }, 300);
                        } else {
                            ModalUI.alert(data.error || 'Failed to delete post', 'Error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ModalUI.alert('An error occurred while deleting post', 'Error');
                    });
            });
        }
    });
    // Report Post
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('report-post')) {
            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('You need to sign in to report posts.');
                return;
            }

            const postId = e.target.dataset.postId;

            // For now, use a simple prompt. In future, could use a proper modal form.
            // Using a hacky way to get input via ModalUI if possible, or fallback to prompt
            // Since ModalUI doesn't have prompt, we'll implement a custom one or just use browser prompt
            const reason = prompt("Please enter a reason for reporting this post:");

            if (reason === null) return; // Cancelled
            if (!reason.trim()) {
                ModalUI.alert("Reason is required.", "Error");
                return;
            }

            fetch('index.php?controller=support&action=createReport', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    report_type: 'social_post',
                    title: 'Reported Post #' + postId,
                    description: reason,
                    priority: 'medium',
                    post_id: postId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        ModalUI.alert("Thank you. The post has been reported for review.", "Report Submitted");
                        // Hide the menu
                        document.getElementById('menu-' + postId).style.display = 'none';
                    } else {
                        ModalUI.alert(data.error || 'Failed to submit report', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    ModalUI.alert('An error occurred while reporting', 'Error');
                });
        }
    });

})();



