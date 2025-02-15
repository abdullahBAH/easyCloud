document.addEventListener('DOMContentLoaded', function () {


    const filesContainer = document.getElementById('filesContainer');
    const contextMenu = document.getElementById('contextMenu');
    let currentView = 'grid';

    // Render files
    function renderFiles() {
        filesContainer.innerHTML = '';
        files.forEach(file => {
            const fileCard = document.createElement('div');
            fileCard.className = 'file-card';
            fileCard.innerHTML = `
                <div class="file-preview">
                    ${file.preview
                    ? `<img src="${file.preview}" alt="${file.name}">
                           ${file.type === 'video'
                        ? '<div class="play-button"><span class="material-icons">play_arrow</span></div>'
                        : ''}`
                    : `<span class="material-icons">${file.icon}</span>`}
                </div>
                <div class="file-info">
                    <div class="file-header">
                        <div class="file-name">
                            <span class="material-icons">${file.icon}</span>
                            <span>${file.name}</span>
                        </div>
                        <button class="more-button">
                            <span class="material-icons info" id="${file.id}">more_vert</span>
                        </button>
                    </div>
                    <div class="file-meta">
                        <span>Upload At  • ${file.created_at}</span>
                    </div>
                </div>
            `;

            // Add context menu event listeners
            fileCard.addEventListener('contextmenu', showContextMenu);
            const moreButton = fileCard.querySelector('.more-button');
            moreButton.addEventListener('click', (e) => {
                e.stopPropagation();
                showContextMenu(e);
            });

            filesContainer.appendChild(fileCard);
        });
    }

    // View toggle
    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', () => {
            const view = button.dataset.view;
            document.querySelectorAll('.view-button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            filesContainer.classList.toggle('list-view', view === 'list');
            currentView = view;
        });
    });

    // Context menu
    function showContextMenu(e) {
        e.preventDefault();
        const x = e.clientX;
        const y = e.clientY;

        contextMenu.style.left = `${x}px`;
        contextMenu.style.top = `${y}px`;
        contextMenu.classList.add('active');
    }

    // Close context menu when clicking outside
    document.addEventListener('click', () => {
        contextMenu.classList.remove('active');
    });

    // Context menu actions
    contextMenu.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const action = item.textContent.toLowerCase();
            console.log(`${action} action triggered`);
            contextMenu.classList.remove('active');
        });
    });


    // Attach click events to info buttons after rendering the files
    function attachInfoClickListeners() {
        document.querySelectorAll(".info").forEach(icon => {
            icon.addEventListener("click", function (e) {
                e.preventDefault(); // Prevent default action (optional)

                const fileId = parseInt(this.id); // Get ID from clicked icon
                const file = files.find(f => f.id === fileId);

                if (file) {
                    // Create menu
                    const menu = document.createElement("ul");
                    menu.classList.add("context-menu");

                    // Create the download link item directly inside <li>
                    const downloadItem = document.createElement("li");
                    downloadItem.classList.add("download-button");

                    // Create the <a> tag
                    const downloadLink = document.createElement("a");
                    downloadLink.href = file.downloadPath; // Set the download URL

                    document.querySelector('.download-button a').href = file.downloadPath;
                    document.querySelector(".delete").setAttribute("data-id", file.deletePath);
                    document.querySelector(".share").setAttribute("data-id", file.sharePath);


                }
            });
        });
    }

    document.querySelectorAll(".delete").forEach(button => {
        button.addEventListener("click", function () {
            const fileUrl = this.getAttribute("data-id");
            alert(fileUrl);  // Check if the URL is being captured correctly
            if (!fileUrl) {
                console.error("No file URL found!");
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(fileUrl, {
                method: "DELETE",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => {
                    if (response.ok) {
                        alert("File deleted successfully!");
                        this.closest("li").remove(); // Remove from UI
                    } else {
                        alert("Failed to delete file.");
                    }
                })
                .catch(error => console.error("Error:", error));
        });
    });

    document.querySelectorAll(".share").forEach(button => {
        button.addEventListener("click", function () {
            const fileId = this.getAttribute("data-id");

            // Send a request to the share route
            fetch(fileId)
                .then(response => response.json())
                .then(data => {
                    if (data.share_link) {
                        // Copy the shareable link to the clipboard
                        navigator.clipboard.writeText(data.share_link)
                            .then(() => {
                            })
                            .catch(error => {
                                console.error('Failed to copy text: ', error);
                                alert("Failed to copy link.");
                            });
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Initial render
    renderFiles();
    // Attach click listeners for info icons
    attachInfoClickListeners();
});
