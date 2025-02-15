<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <link rel="icon" href="{{ asset('images/upload-ico.png') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="app-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <h1>Welcome to Drive</h1>
            </div>

            <div class="header-right">
                {{-- @if (session('success'))
                    <p style="color: green;">{{ session('success') }}</p>
                    <p>تم رفع الملف: <a href="{{ asset('uploads/' . session('file')) }}"
                            target="_blank">{{ session('file') }}</a></p>
                @endif --}}
                {{-- @if (session('success'))
                    <p style="color: green;">{{ session('success') }}</p>
                    <p>تم رفع الملف: <a href="{{ asset('uploads/' . session('file')) }}"
                            target="_blank">{{ session('file') }}</a></p>
                @endif --}}
                @if (session('error'))
                    <div style="color: red; font-weight: bold; margin-bottom: 10px;">
                        {{ session('error') }}
                    </div>
                @endif



                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data"
                    style="display: flex;  gap: 15px;" class="view-button">
                    @csrf

                    <!-- File Input -->
                    <input type="file" name="file" required
                        style="padding: 8px; border-radius: 4px; width: 50%; border: 1px solid #ccc; font-size: 16px; cursor: pointer;">

                    <!-- Submit Button (Single-line) -->
                    <button type="submit"
                        style="padding: 8px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-align: center;">
                        Upload File
                    </button>
                </form>




                <button class="view-button" data-view="list">
                    <span class="material-icons">view_list</span>
                </button>
                <button class="view-button active" data-view="grid">
                    <span class="material-icons">grid_view</span>
                </button>
                <button class="info-button">
                    <span class="material-icons">info</span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <section class="files-section">
                <h2>Suggested files</h2>
                <div class="files-grid" id="filesContainer">
                    <!-- Files will be inserted here by JavaScript -->
                </div>
            </section>
        </main>

        <!-- Context Menu -->
        <div class="context-menu" id="contextMenu">
            <ul>
                <li class="download-button">
                    <a href="" download>
                        <span class="material-icons">download</span> Download
                    </a>
                </li>
                <li class="share" data-id=""> <span class="material-icons">share</span>
                    Share
                </li>
                {{-- <li><span class="material-icons">drive_file_move</span>Move</li> --}}
                <li class="delete" data-id="http://127.0.0.1:8000/file/1"> <span class="material-icons">delete</span>
                    Delete
                </li>
            </ul>
        </div>
    </div>
    <script>
        const files = @json($files); // تحميل الملفات المرسلة من Laravel
    </script>

    <script src="{{ asset('js/main.js?tet') }}"></script>
</body>

</html>
