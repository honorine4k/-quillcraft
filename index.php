<?php
session_start();
require "./frontend/logics/db_connect.php";

if (empty($_SESSION['user'])) {
    header("location:./frontend/login.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrapesJS Drag and Drop Editor</title>
    <!-- Include GrapesJS styles -->
    <link href="./grapes.min.css" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css/normalize.css"/> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ff0046;
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
        }



        #gjs {
            /* position: relative; */
            width: 100%;
            height: 60vh;
        }


        /* General style for the Style Manager panel */
        :root {
            --main: #bd0034;
            /* Main theme color */
            --light: #ffffff;
            /* Light color, used for text or backgrounds */
            --dark: #000000;
            /* Dark color, typically used for text */
            --bg: #ff0046;
            /* Background color for highlighted elements */
        }

        /* General styles for the editor */
        /* General Editor Styles */
        .gjs-pn-panels {
            background-color: var(--bg);
            color: var(--light);
        }

        .gjs-pn-buttons .gjs-pn-btn {
            background-color: var(--main);
            color: var(--light);
            border: none;
        }

        .gjs-pn-buttons .gjs-pn-btn:hover {
            background-color: var(--dark);
        }

        /* Style the canvas background */
        .gjs-cv-canvas {
            background-color: var(--main);
            border-color: var(--main);
            width: 100%;
        }

        /* Specific styles for any highlighted text or active controls */
        .gjs-pn-active {
            border-color: var(--main);
        }

        /* Style Manager Panel Styles */
        .gjs-sm-sectors {
            background-color: var(--main);
            color: var(--light);
        }

        .gjs-sm-title {
            background-color: var(--dark);
            color: var(--light);
        }

        .gjs-sm-properties {
            background-color: var(--dark);
            color: var(--light);
        }

        .gjs-field {
            background-color: var(--light);
            color: var(--dark);
            border: 1px solid var(--main);
        }

        .gjs-sm-sector .gjs-sm-title {
            background-color: var(--light);
            color: var(--dark);
        }

        .gjs-cv-canvas {
            /* targeting the canvas */
            min-height: 100%;

        }

        .gjs-block {
            /* targeting toolbox elements */
            flex-basis: auto;
        }

        .buttons {
            width: 100%;
            margin: auto;
            height: 50px;
            display: flex;
            flex-direction: row;
            gap: 10px;
            background: #ff0046;
            color: white;
            position: relative;
            left: 20px;
        }

        input {
            margin: 4px;
            padding: 10px;
            /* background-color: var(--dark); */
            color: var(--dark);
        }

        button {

            color: #fff;
            border: none;
            padding: 10px 10px;
            margin: 10px;
            cursor: pointer;
            font-family: "Open Sans", sans-serif;
            position: relative;
            left: 10px;
        }

        button:hover {
            background-color: var(--light);
            color: var(--main);
            border: 2px solid var(--main);
        }

        #visibility-panel {
            position: fixed;
            z-index: 1000;
            bottom: 10px;
            right: 10px;
        }

        .gjs-pn-panels {
            transition: all 0.3s ease-in-out;
        }



        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        section {
            width: 300px;
            background-color: var(--main);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        section select {
            width: 100%;
            margin: auto;
            height: 40px;
        }

        section select option {
            color: var(--main);
            padding: 5px;
            text-align: center;
        }

        section h5 {
            text-align: center;
            color: var(--light);
        }

        section button {
            background-color: var(--light);
            color: var(--main);
            transition: 0.7s;
            width: 100%;
            margin: auto;
        }

        section button:hover {
            background-color: var(--light);
            color: var(--main);
            transform: scale(1.1);
        }

        section textarea {
            padding: 10px;
        }

        section input {
            width: 100%;
            height: 20px;
            margin: auto;
        }

        a {
            text-decoration: none;
            position: relative;
            left: 90%;
            top: 20px;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Container for the editor -->
    <div class="buttons">
        <a href="./frontend/index.php">Home</a>
    </div>

    <div id="gjs"></div>

    <div id="saveModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; z-index:9999;">
        <h4>Save Template</h4>
        <input type="text" id="templateDesc" placeholder="Enter description" style="width:100%;">
        <input type="file" id="templateImage" style="width:100%; margin-top:10px;">
        <button onclick="saveTemplate()">Save</button>
        <button onclick="closeSaveModal()">Cancel</button>
    </div>

    <!-- Include GrapesJS script -->
    <script src="./grapes.min.js"></script>
    <script src="https://unpkg.com/grapesjs-blocks-basic"></script> <!-- Basic blocks -->
    <script src="https://unpkg.com/grapesjs-navbar"></script>
    <script>
        var editor = grapesjs.init({
            container: "#gjs",
            width: 'auto',
            height: '100vh',
            fromElement: true,
            showOffsets: 1,
            allowScripts: 1,
            assetManager: {
                autoAdd: true,
                multiUpload: true,
                // Server-side script tbo handle uploads
                uploadName: 'files',
                upload: 'handler.php',
                multiUpload: true,
                assets: [],
                assetsCategory: 'Images', // Display only images
                showBtnUpload: true, // Show upload button
                btnText: 'Upload Image', // Text for the upload button
                modalTitle: 'Select Image', // Title for the upload modal
                modalBtnClose: true, // Show close button in the modal
                modalBtnLabel: 'Close',
                dropzone: 0,
                addBtnText: 'Add image',
                modalTitle: 'Select Image',
                openAssetsOnDrop: 1,
                handleAdd: (responseText, complete) => {
                    const result = JSON.parse(responseText);
                    if (result.data) {
                        result.data.forEach(asset => {
                            editor.AssetManager.add(asset);
                        });
                        complete();
                    }
                }
            },
            deviceManager: {
                devices: [{
                        name: 'Desktop',
                        width: '', // Default size
                    },
                    {
                        name: 'Tablet',
                        width: '768px', // Example for tablets
                    },
                    {
                        name: 'Mobile',
                        width: '320px', // Example for mobile phones
                    },
                ]
            },
            storageManager: {
                id: 'gjs-', // Prefix identifier that will be used inside storing and loading
                type: 'local', // Type of the storage
                autosave: false, // Store data automatically
                autoload: false, // Autoload stored data on init
                stepsBeforeSave: 1, // If autosave enabled, indicates how many changes are necessary before store method is triggered
            },

            styleManager: {
                clearProperties: 1
            },
            plugins: [
                'gjs-preset-webpage', 'grapesjs-lory-slider',
                'gjs-blocks-basic', 'grapesjs-tabs', 'grapesjs-custom-code', 'gjs-plugin-ckeditor', 'grapesjs-blocks-basic', 'grapesjs-navbar', 'gjs-preset-webpage'
            ],
            pluginsOpts: {
                'grapesjs-blocks-basic': {},
                'gjs-preset-webpage': {
                    /* options */
                },
                'grapesjs-navbar': {},

                'grapesjs-lory-slider': {
                    sliderBlock: {
                        category: 'Extra'
                    }
                },
                'grapesjs-tabs': {
                    tabsBlock: {
                        category: 'Extra'
                    }
                },
                'gjs-plugin-ckeditor': {
                    language: 'en'
                },
                'gjs-preset-webpage': {
                    modalImportTitle: 'Import Template',
                    modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                    modalImportContent: function(editor) {
                        return editor.getHtml() + '<style>' + editor.getCss() + '</style>'
                    },
                    filestackOpts: {
                        key: 'AYmqZc2e8RLGLE7TGkX3Hz'
                    },
                    aviaryOpts: false,
                    blocksBasicOpts: {
                        flexGrid: 1
                    },
                    customStyleManager: [{
                        name: 'General',
                        buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                        properties: [{
                                name: 'Alignment',
                                property: 'float',
                                type: 'radio',
                                defaults: 'none',
                                list: [{
                                        value: 'none',
                                        className: 'fa fa-times'
                                    },
                                    {
                                        value: 'left',
                                        className: 'fa fa-align-left'
                                    },
                                    {
                                        value: 'right',
                                        className: 'fa fa-align-right'
                                    }
                                ],
                            },
                            {
                                property: 'position',
                                type: 'select'
                            }
                        ],
                    }, {
                        name: 'Dimension',
                        open: false,
                        buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                        properties: [{
                            id: 'flex-width',
                            type: 'integer',
                            name: 'Width',
                            units: ['px', '%'],
                            property: 'flex-basis',
                            toRequire: 1,
                        }, {
                            property: 'margin',
                            properties: [{
                                    name: 'Top',
                                    property: 'margin-top'
                                },
                                {
                                    name: 'Right',
                                    property: 'margin-right'
                                },
                                {
                                    name: 'Bottom',
                                    property: 'margin-bottom'
                                },
                                {
                                    name: 'Left',
                                    property: 'margin-left'
                                }
                            ],
                        }, {
                            property: 'padding',
                            properties: [{
                                    name: 'Top',
                                    property: 'padding-top'
                                },
                                {
                                    name: 'Right',
                                    property: 'padding-right'
                                },
                                {
                                    name: 'Bottom',
                                    property: 'padding-bottom'
                                },
                                {
                                    name: 'Left',
                                    property: 'padding-left'
                                }
                            ],
                        }],
                    }, {
                        name: 'Typography',
                        open: false,
                        buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                        properties: [{
                                name: 'Font',
                                property: 'font-family'
                            },
                            {
                                name: 'Weight',
                                property: 'font-weight'
                            },
                            {
                                name: 'Font color',
                                property: 'color'
                            },
                            {
                                property: 'text-align',
                                type: 'radio',
                                defaults: 'left',
                                list: [{
                                        value: 'left',
                                        name: 'Left',
                                        className: 'fa fa-align-left'
                                    },
                                    {
                                        value: 'center',
                                        name: 'Center',
                                        className: 'fa fa-align-center'
                                    },
                                    {
                                        value: 'right',
                                        name: 'Right',
                                        className: 'fa fa-align-right'
                                    },
                                    {
                                        value: 'justify',
                                        name: 'Justify',
                                        className: 'fa fa-align-justify'
                                    }
                                ],
                            }, {
                                property: 'text-decoration',
                                type: 'radio',
                                defaults: 'none',
                                list: [{
                                        value: 'none',
                                        name: 'None',
                                        className: 'fa fa-times'
                                    },
                                    {
                                        value: 'underline',
                                        name: 'underline',
                                        className: 'fa fa-underline'
                                    },
                                    {
                                        value: 'line-through',
                                        name: 'Line-through',
                                        className: 'fa fa-strikethrough'
                                    }
                                ],
                            }, {
                                property: 'text-shadow',
                                properties: [{
                                        name: 'X position',
                                        property: 'text-shadow-h'
                                    },
                                    {
                                        name: 'Y position',
                                        property: 'text-shadow-v'
                                    },
                                    {
                                        name: 'Blur',
                                        property: 'text-shadow-blur'
                                    },
                                    {
                                        name: 'Color',
                                        property: 'text-shadow-color'
                                    }
                                ],
                            }
                        ],
                    }, {
                        name: 'Decorations',
                        open: false,
                        buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                        properties: [{
                            type: 'slider',
                            property: 'opacity',
                            defaults: 1,
                            step: 0.01,
                            max: 1,
                            min: 0,
                        }, {
                            property: 'border-radius',
                            properties: [{
                                    name: 'Top',
                                    property: 'border-top-left-radius'
                                },
                                {
                                    name: 'Right',
                                    property: 'border-top-right-radius'
                                },
                                {
                                    name: 'Bottom',
                                    property: 'border-bottom-left-radius'
                                },
                                {
                                    name: 'Left',
                                    property: 'border-bottom-right-radius'
                                }
                            ],
                        }, {
                            property: 'box-shadow',
                            properties: [{
                                    name: 'X position',
                                    property: 'box-shadow-h'
                                },
                                {
                                    name: 'Y position',
                                    property: 'box-shadow-v'
                                },
                                {
                                    name: 'Blur',
                                    property: 'box-shadow-blur'
                                },
                                {
                                    name: 'Spread',
                                    property: 'box-shadow-spread'
                                },
                                {
                                    name: 'Color',
                                    property: 'box-shadow-color'
                                },
                                {
                                    name: 'Shadow type',
                                    property: 'box-shadow-type'
                                }
                            ],
                        }, {
                            property: 'background',
                            properties: [{
                                    name: 'Image',
                                    property: 'background-image'
                                },
                                {
                                    name: 'Repeat',
                                    property: 'background-repeat'
                                },
                                {
                                    name: 'Position',
                                    property: 'background-position'
                                },
                                {
                                    name: 'Attachment',
                                    property: 'background-attachment'
                                },
                                {
                                    name: 'Size',
                                    property: 'background-size'
                                }
                            ],
                        }, ],
                    }, {
                        name: 'Extra',
                        open: false,
                        buildProps: ['transition', 'perspective', 'transform'],
                        properties: [{
                            property: 'transition',
                            properties: [{
                                    name: 'Property',
                                    property: 'transition-property'
                                },
                                {
                                    name: 'Duration',
                                    property: 'transition-duration'
                                },
                                {
                                    name: 'Easing',
                                    property: 'transition-timing-function'
                                }
                            ],
                        }, {
                            property: 'transform',
                            properties: [{
                                    name: 'Rotate X',
                                    property: 'transform-rotate-x'
                                },
                                {
                                    name: 'Rotate Y',
                                    property: 'transform-rotate-y'
                                },
                                {
                                    name: 'Rotate Z',
                                    property: 'transform-rotate-z'
                                },
                                {
                                    name: 'Scale X',
                                    property: 'transform-scale-x'
                                },
                                {
                                    name: 'Scale Y',
                                    property: 'transform-scale-y'
                                },
                                {
                                    name: 'Scale Z',
                                    property: 'transform-scale-z'
                                }
                            ],
                        }]
                    }, {
                        name: 'Flex',
                        open: false,
                        properties: [{
                            name: 'Flex Container',
                            property: 'display',
                            type: 'select',
                            defaults: 'block',
                            list: [{
                                    value: 'block',
                                    name: 'Disable'
                                },
                                {
                                    value: 'flex',
                                    name: 'Enable'
                                }
                            ],
                        }, {
                            name: 'Flex Parent',
                            property: 'label-parent-flex',
                            type: 'integer',
                        }, {
                            name: 'Direction',
                            property: 'flex-direction',
                            type: 'radio',
                            defaults: 'row',
                            list: [{
                                value: 'row',
                                name: 'Row',
                                className: 'icons-flex icon-dir-row',
                                title: 'Row',
                            }, {
                                value: 'row-reverse',
                                name: 'Row reverse',
                                className: 'icons-flex icon-dir-row-rev',
                                title: 'Row reverse',
                            }, {
                                value: 'column',
                                name: 'Column',
                                title: 'Column',
                                className: 'icons-flex icon-dir-col',
                            }, {
                                value: 'column-reverse',
                                name: 'Column reverse',
                                title: 'Column reverse',
                                className: 'icons-flex icon-dir-col-rev',
                            }],
                        }, {
                            name: 'Justify',
                            property: 'justify-content',
                            type: 'radio',
                            defaults: 'flex-start',
                            list: [{
                                value: 'flex-start',
                                className: 'icons-flex icon-just-start',
                                title: 'Start',
                            }, {
                                value: 'flex-end',
                                title: 'End',
                                className: 'icons-flex icon-just-end',
                            }, {
                                value: 'space-between',
                                title: 'Space between',
                                className: 'icons-flex icon-just-sp-bet',
                            }, {
                                value: 'space-around',
                                title: 'Space around',
                                className: 'icons-flex icon-just-sp-ar',
                            }, {
                                value: 'center',
                                title: 'Center',
                                className: 'icons-flex icon-just-sp-cent',
                            }],
                        }, {
                            name: 'Align',
                            property: 'align-items',
                            type: 'radio',
                            defaults: 'center',
                            list: [{
                                value: 'flex-start',
                                title: 'Start',
                                className: 'icons-flex icon-al-start',
                            }, {
                                value: 'flex-end',
                                title: 'End',
                                className: 'icons-flex icon-al-end',
                            }, {
                                value: 'stretch',
                                title: 'Stretch',
                                className: 'icons-flex icon-al-str',
                            }, {
                                value: 'center',
                                title: 'Center',
                                className: 'icons-flex icon-al-center',
                            }],
                        }, {
                            name: 'Flex Children',
                            property: 'label-parent-flex',
                            type: 'integer',
                        }, {
                            name: 'Order',
                            property: 'order',
                            type: 'integer',
                            defaults: 0,
                            min: 0
                        }, {
                            name: 'Flex',
                            property: 'flex',
                            type: 'composite',
                            properties: [{
                                name: 'Grow',
                                property: 'flex-grow',
                                type: 'integer',
                                defaults: 0,
                                min: 0
                            }, {
                                name: 'Shrink',
                                property: 'flex-shrink',
                                type: 'integer',
                                defaults: 0,
                                min: 0
                            }, {
                                name: 'Basis',
                                property: 'flex-basis',
                                type: 'integer',
                                units: ['px', '%', ''],
                                unit: '',
                                defaults: 'auto',
                            }],
                        }, {
                            name: 'Align',
                            property: 'align-self',
                            type: 'radio',
                            defaults: 'auto',
                            list: [{
                                value: 'auto',
                                name: 'Auto',
                            }, {
                                value: 'flex-start',
                                title: 'Start',
                                className: 'icons-flex icon-al-start',
                            }, {
                                value: 'flex-end',
                                title: 'End',
                                className: 'icons-flex icon-al-end',
                            }, {
                                value: 'stretch',
                                title: 'Stretch',
                                className: 'icons-flex icon-al-str',
                            }, {
                                value: 'center',
                                title: 'Center',
                                className: 'icons-flex icon-al-center',
                            }],
                        }]
                    }],
                },
            },


        });



        editor.Panels.addPanel({
            id: 'asset-manager-panel',
            el: '.panel__right', // Selector for the panel element
            visible: true, // Whether the panel is visible by default
        });
        editor.Panels.addPanel({
            id: 'my-images-panel',
            visible: true,
            buttons: [{
                id: 'view-images',
                className: 'fa fa-image', // assuming you have FontAwesome
                command: 'open-images-panel',
                active: false,
                togglable: true,
            }, ],
        });



        editor.Panels.addButton('options', [{
            id: 'upload-image',
            className: 'fa fa-upload', // Use Font Awesome icon
            command: 'upload-image',
            attributes: {
                title: 'Upload Image'
            }
        }]);

        // Define the command for the upload button
        editor.Commands.add('upload-image', {
            run: function(editor) {
                var inputFile = document.createElement('input');
                inputFile.type = 'file';
                inputFile.onchange = function(e) {
                    var file = e.target.files[0];
                    uploadImage(file);
                };
                inputFile.click(); // simulate click to open file dialog
            }
        });

        // Function to handle the image upload
        function uploadImage(file) {
            var formData = new FormData();
            formData.append('file', file);

            fetch('load.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.asset) {
                        editor.AssetManager.add(data.asset); // Add uploaded image to asset manager
                        console.log('Image uploaded successfully!');
                    }
                })
                .catch(error => console.error('Error uploading image:', error));
        };

        function uploadImages() {
            const input = document.getElementById('upload-image');
            const files = input.files;
            const {
                AssetManager
            } = editor;

            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    AssetManager.add({
                        src: event.target.result,
                        name: file.name,
                        type: 'image'
                    });
                };
                reader.readAsDataURL(file);
            }
        }


        window.onload = function loadAssets() {
            fetch('./assets.json') // Path to your JSON file
                .then(response => response.json())
                .then(data => {
                    // Clear existing assets
                    editor.AssetManager.getAll().reset();

                    // Add new assets from JSON
                    data.forEach(asset => {
                        editor.AssetManager.add({
                            src: asset.src,
                            type: asset.type,
                            name: asset.name || 'Unnamed image'
                        });
                    });
                })
                .catch(err => console.error('Failed to load assets:', err));
        };

        function getData() {
            fetch('./posts/images')
                .then(response => response.json())
                .then(data => {
                    if (data && data.data) {
                        data.data.forEach(asset => {
                            editor.AssetManager.add(asset);
                        });
                    }
                })
                .catch(console.error);
        };
        editor.Panels.addPanel({
            id: 'panel-top',
            el: '.panel__top',
        });

        editor.Panels.addButton('options', [{
            id: 'view-assets',
            className: 'fa fa-picture-o', // Using FontAwesome icon class
            command: 'open-asset',
            attributes: {
                title: 'View Assets'
            }
        }]);

        // Command to open the Asset Manager
        editor.Commands.add('open-asset', {
            run: async (editor, sender, options) => {
                // Await the getData function to complete fetching data
                await getData();

                // Once data is fetched and presumably loaded into the Asset Manager,
                // open the Asset Manager
                editor.AssetManager.open();
            },
            stop: (editor) => {
                // Close the Asset Manager when the command is stopped
                editor.AssetManager.close();
            },
        });
        // Load assets when the editor is ready

        editor.setStyle(`
    .navbar {
      padding: 10px;
      background-color: #eee;
      box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }
    .nav-link {
      color: #333;
      padding: 10px;
      text-decoration: none;
    }
    .nav-link:hover {
      text-decoration: underline;
    }
`);


        // Toggle popup visibility
        editor.Panels.addButton('options', [{
            id: 'save-db',
            className: 'fa fa-save',
            command: 'save-db',
            attributes: {
                title: 'Save DB'
            }
        }]);

        editor.Commands.add('save-db', {
            run: function(editor, sender) {
                sender.set('active', false);
                openSaveModal();
            }
        });
        // Get the modal

        function openSaveModal() {
            document.getElementById('saveModal').style.display = 'block';
        }

        function closeSaveModal() {
            document.getElementById('saveModal').style.display = 'none';
        }

        // Handle form submission
        function saveTemplate() {
            var description = document.getElementById('templateDesc').value;
            var imageFile = document.getElementById('templateImage').files[0];
            var formData = new FormData();
            formData.append('description', description);
            formData.append('image', imageFile);
            formData.append('template', JSON.stringify(editor.getComponents()));
            formData.append('style', JSON.stringify(editor.getStyle()));

            fetch('./save_template.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    alert('Template saved successfully!');
                    closeSaveModal();
                })
                .catch(error => console.error('Error:', error));
        }
        // Add UI to adjust canvas size based on a dropdown or input fields

        // Add button to the panel
        editor.Panels.addButton('options', [{
            id: 'save-template',
            className: 'fa fa-save',
            command: 'open-save-modal',
            attributes: {
                title: 'Save Template'
            }
        }]);

        // Define the command
        editor.Commands.add('open-save-modal', {
            run: function(editor, sender) {
                sender.set('active', 0); // toggle button active state
                openPostModal(); // Function to create and handle the popup
            }
        });

        function openPostModal() {
            // Simple modal structure
            const modalContent = `
                <h5>Publish your work</h5>
                <input type="text" id="template-title" placeholder="Title" />
                <textarea id="template-description" placeholder="Description"></textarea>
                <select id="template-category">
                    <option value="food">Food</option>
                    <option value="tour">Tour and Travel</option>
                    <option value="fashion">Fashion</option>
                    <option value="music">Music</option>
                    <option value="art">Art</option>
                </select>
                

                <button onclick="postData()">Publish</button>
                `;
            const modal = document.createElement('section');
            modal.innerHTML = modalContent;
            modal.style.position = 'fixed';
            modal.style.top = '50%';
            modal.style.left = '50%';
            modal.style.transform = 'translate(-50%, -50%)';
            modal.style.padding = '20px';
            modal.style.zIndex = '1001';
            document.body.appendChild(modal);

            function closeModal() {
                modal.style.display = 'none';
            }
        };




        function postData() {
            const title = document.getElementById('template-title').value;
            const description = document.getElementById('template-description').value;
            const category = document.getElementById('template-category').value;
            const html = editor.getHtml();
            const htmlContent = `<head><link rel="stylesheet" type="text/css" href="./styles.css"/></head>
    ${html}
    <div class="footer">
        <div class="form">       
            <form action="./php/recieve.php" method="post">
                <div class="like">
                    <h1>Place a like here</h1>
                    <div class="links">
                        <div class="link">
                            <input type="radio" name="like" value="1" id="like">
                            <label for="like">Like</label>
                        </div>
                        <div class="link">
                            <input type="radio" name="like" value="-1"  id="dislike">
                            <label for="dislike">Dislike</label>
                        </div>
                    </div>
                </div>
                <div class="bottom">
                    <h1>Place a comment here</h1>
                    <!php if(isset($_GET['error'])){ ?>
                        <div class="error" style="color: var(--main); background-color: var(--light); text-align:center; padding: 10px;">
                            <^php echo $_GET['error']?>
                        </div>
                    <@php }?>'
                    <textarea name="comment" id="" cols="30" rows="8" placeholder="type in here please"></textarea>
                    <input type="submit" value="Submit" name="submit">
                </div>
            </form> 
        </div>
    </div>`;
            const newHtml = htmlContent.replace('./posts/images/', '../../images/')
            const newerHtml = newHtml.replace('!', "?");
            const newestHtml = newerHtml.replace('^', "?");
            const updatedHtml = newestHtml.replace('@', "?");
            const css = editor.getCss();
            const updatedCSS = `
        :root {
        --main: #bd0034;
        --light: #ffffff;
        --dark: #000000;
        --bg: #ff0046;
        }
        .footer{
            width: 100%;
            padding: 10px;
            height: 300px;
            background-color:var(--light);
        }
        .footer .form{
            display: flex;
            flex-direction: row;
            gap: 20px;
            
        }
    
        .footer .form .like{
            width: 30%;
            height: 200px;
            border-radius:10px;
            background-color: var(--light);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .footer .form .like h1{
            color: var(--main);
        }

        .footer .form .like .links{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .footer .form .like .links label{
            text-decoration: none;
            color: var(--light);
            background-color: var(--main);
            padding: 10px;
            transition: 0.7s;
        
        }
        .footer .form .like .links input[type="radio"]{
            display: none;
        }
        .footer .form .like .links input[type="radio"]:checked~label{
            background-color: var(--light);
            color: var(--main);
            box-shadow: 0px 5px 6px var(--dark);
        }   
        .footer .form .like .links label:hover{
            background-color: var(--light);
            color: var(--main);
            transform: scale(1.2);
            box-shadow: 0px 5px 6px var(--dark);
        }

        .footer form{
            display: flex;
            width: 80%;
            margin: auto;
            flex-direction: row;
            gap:40px;
            align-items: center;
            justify-content: center;
            background-color: var(--main);
            padding: 10px;

            border-radius: 20px;
        }
        .footer form h1{
            text-align: center;
            color:var(--light);
        }
        .footer form textarea{
            width: 300px;
            border-radius: 20px;
            padding:20px; 
            margin: auto;
        }
        .footer form input[type="submit"]{
            width:140px;
            padding:10px;
            margin: auto;
            background-color: var(--light);
            color: var(--main);
            border: none;
            transition: 0.7s;
        }
        .footer form input[type="submit"]:hover{
            background-color: var(--main);
            color: var(--light);
            cursor: pointer;
            box-shadow: 0px 5px 6px var(--dark);
        }
        .footer .bottom{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap:20px;
        }
        ${css}
    `;
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
            formData.append('category', category);
            formData.append('html', updatedHtml);
            formData.append('css', updatedCSS);

            fetch('./post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    alert('Template saved successfully!');
                    document.body.removeChild(document.querySelector('div')); // remove modal from view
                })
                .catch(error => console.error('Error:', error));
        }


        // Define the command to toggle the visibility of the main panels
        function change() {
            const panels = document.querySelector('.gjs-pn-panels');
            if (panels.style.display === 'none') {
                panels.style.display = '';
                // Change button state if needed
            } else {
                panels.style.display = 'none';
                // Change button state if needed
            }
        };


        editor.Panels.addPanel({
            id: 'panel-top',
            el: '.panel__top',
        });







        editor.Commands.add('set-canvas-size', {
            run: function(editor, sender) {
                sender.set('active', 0); // Toggle button active state
                const canvasWidth = prompt("Enter the canvas width (px):");
                if (canvasWidth) {
                    editor.Canvas.setDimensions({
                        width: canvasWidth
                    });
                }
            }
        });

        editor.on('load', () => {
            const cssComposer = editor.CssComposer;
            const defaultStyle = cssComposer.addRule('body', {
                width: '100vw'
            });
        });
        editor.on('load', () => {
            editor.setStyle(`
        body {
            margin: 0;
            width: 100%;
            min-height: 100vh;  // Ensures full vertical space is taken as well
        }
    `);
        });

        editor.on('load', () => {
            const cssComposer = editor.CssComposer;
            const rule = cssComposer.addRule('body', {
                'width': '100%'
            });

            rule.addMediaCondition('max-width: 600px', {
                'width': '100%'
            });
        });

        // This is optional but useful for immediately seeing a navbar
        editor.on('load', () => {
            const bm = editor.BlockManager;

            if (bm.get('navbar')) {
                editor.addComponents(bm.get('navbar').content);
            }
        });
        editor.CssComposer.addRules([{
                selector: '.navbar',
                style: {
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    padding: '10px',
                    backgroundColor: '#333',
                    color: '#fff'
                }
            },
            {
                selector: '.nav-link',
                style: {
                    color: '#fff',
                    textDecoration: 'none',
                    marginLeft: '10px'
                }
            },
            {
                selector: '.nav-link:hover',
                style: {
                    textDecoration: 'underline'
                }
            }
        ]);
        editor.DomComponents.addType('link', {
            isComponent: el => el.tagName === 'A',
            model: {
                defaults: {
                    traits: [{
                            type: 'text',
                            label: 'href',
                            name: 'href',
                            placeholder: 'https://example.com'
                        },
                        {
                            type: 'text',
                            label: 'Text',
                            name: 'content',
                            changeProp: 1
                        }
                    ],
                    content: 'Edit link text'
                },
                init() {
                    this.on('change:content', this.updateContent);
                },
                updateContent() {
                    this.set('content', this.get('content'));
                }
            }
        });


        // Now, when you add a navbar, links inside will have your custom traits.

        editor.Panels.addPanel({
            id: 'top-panel',
            visible: true,
            buttons: [{
                id: 'toggle-blocks',
                className: 'fa fa-th', // Using Font Awesome icon class
                command: 'toggle-blocks-panel',
                active: false, // Initial state of the button
                attributes: {
                    title: 'Toggle Blocks Panel'
                }
            }]
        });
        window.addEventListener('resize', function() {
            const editorWidth = window.innerWidth;
            const editorHeight = window.innerHeight;

            // You can adjust more specific parts of the editor if needed
            editor.Canvas.setDimensions({
                width: editorWidth,
                height: editorHeight
            });
        });
        editor.on("component:selected", model => {
            if (model.attributes.type == "text") {
                var dom = model.view.$el[0];
                var event = new MouseEvent('dblclick', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': true
                });
                $("iframe").contents().find(`.${dom.classList[0]}`)[0].dispatchEvent(event);
                $("iframe").contents().find(`.${dom.classList[0]}`).dblclick()
            }
        })




        editor.BlockManager.add('simple-div', {
            label: 'Simple Div',
            content: '<div style="padding:10px; margin:10px; border: 1px solid #ccc">Simple Div</div>',
            category: 'Basic',
        });

        // Add a div with class and ID
        editor.BlockManager.add('styled-div', {
            label: 'Styled Div',
            content: '<div id="unique-id" class="my-custom-class" style="padding:20px; background-color: #f0f0f0; border-radius: 8px;">Styled Div</div>',
            category: 'Styled',
        });


        //this codes add elements into the editor
        editor.BlockManager.add('header', {
            label: 'Header',
            content: '<header><h1>This is a header</h1></header>',
            category: 'Basic'
        });

        editor.BlockManager.add('paragraph', {
            label: 'Paragraph',
            content: '<p>This is a paragraph</p>',
            category: 'Basic'
        });

        editor.BlockManager.add('button', {
            label: 'Button',
            content: '<button>Click me</button>',
            category: 'Basic'
        });

        editor.BlockManager.add('image', {
            label: 'Image',
            content: '<img src="http://placehold.it/350x250" alt="Placeholder"/>',
            category: 'Media'
        });


        const blockManager = editor.BlockManager;

        blockManager.add('section', {
            label: 'Section',
            content: '<section><h1>Section Title</h1><p>Content here</p></section>',
            category: 'Basic'
        });

        blockManager.add('header', {
            label: 'Header',
            content: '<header><h1>Header Title</h1></header>',
            category: 'Basic'
        });

        blockManager.add('article', {
            label: 'Article',
            content: '<article><h1>Article Title</h1><p>Some text here</p></article>',
            category: 'Basic'
        });

        blockManager.add('footer', {
            label: 'Footer',
            content: '<footer><p>Footer content here</p></footer>',
            category: 'Basic'
        });

        blockManager.add('address', {
            label: 'Address',
            content: '<address>Contact me here</address>',
            category: 'Basic'
        });



        blockManager.add('image', {
            label: 'Image',
            content: '<img src="http://placehold.it/350x250" alt="Placeholder">',
            category: 'Media'
        });

        blockManager.add('link', {
            label: 'Link',
            content: '<a href="#">Click me</a>',
            category: 'Basic'
        });

        blockManager.add('list', {
            label: 'List',
            content: '<ul><li>List Item 1</li><li>List Item 2</li></ul>',
            category: 'Basic'
        });

        blockManager.add('table', {
            label: 'Table',
            content: '<table><tr><th>Header</th><th>Header</th></tr><tr><td>Data</td><td>Data</td></tr></table>',
            category: 'Basic'
        });

        blockManager.add('form', {
            label: 'Form',
            content: '<form><input type="text" placeholder="Enter text..."><button type="submit">Submit</button></form>',
            category: 'Forms'
        });

        blockManager.add('input', {
            label: 'Input',
            content: '<input type="text" placeholder="Enter text...">',
            category: 'Forms'
        });


        // Function to load images into the Asset Manager

        fetch('./assets.json') // Replace 'images.json' with the path to your JSON file
            .then(response => response.json())
            .then(images => {
                images.forEach(image => {
                    editor.AssetManager.add(image);
                });
            })
            .catch(error => {
                console.error('Error loading images:', error);
            });


        // Call the function on editor load or whenever appropriate


        editor.BlockManager.add('image', {
            label: 'Image',
            category: 'Basic',
            content: {
                type: 'image',
                activeOnRender: 1
            },
            attributes: {
                class: 'fa fa-image'
            }
        });



        editor.on('asset:upload:response', (response) => {
            // Handle the server response after uploading an image
            console.log(response);
        });



        document.getElementById('post-data').addEventListener('click', function() {
            const html = editor.getHtml();
            const htmlContent = `<head><link rel="stylesheet" type="text/css" href="./styles.css"/></head>${html}`;
            const cssContent = editor.getCss();

            var updatedHtml = htmlContent.replace('./edits/images/', './images/')

            fetch('./php_script.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        html: updatedHtml,
                        css: cssContent
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    alert('Data posted successfully!');
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Failed to post data');
                });
        });


        document.getElementById('save-btn').addEventListener('click', function() {
            var editorData = {
                html: editor.getHtml(),
                css: editor.getCss(),
                components: editor.getComponents(),
                styles: editor.getStyle()
            };

            var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(editorData));
            var dlAnchorElem = document.createElement('a');
            dlAnchorElem.setAttribute("href", dataStr);
            dlAnchorElem.setAttribute("download", "project.json");
            dlAnchorElem.click();
        });

        document.getElementById('load-btn').addEventListener('click', function() {
            var input = document.getElementById('load-input');
            if (input.files.length > 0) {
                var file = input.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    var result = JSON.parse(e.target.result);
                    editor.setComponents(result.components);
                    editor.setStyle(result.styles);
                    // Assuming you want to replace the entire HTML and CSS
                    editor.setHtml(result.html);
                    editor.setCss(result.css);
                };

                reader.readAsText(file);
            }
        });


        // Custom command to apply background opacity
        editor.Commands.add('set-bg-opacity', {
            run: function(editor, sender, opts = {}) {
                const selected = editor.getSelected();
                if (selected) {
                    let opacityValue = opts.opacity / 100; // Convert percentage to decimal
                    selected.addStyle({
                        'background-color': `rgba(${opts.color.r}, ${opts.color.g}, ${opts.color.b}, ${opacityValue})`
                    });
                }
            }
        });

        editor.DomComponents.addType('div', {
            model: {
                defaults: {
                    traits: [{
                            name: 'id',
                            label: 'ID'
                        },
                        {
                            name: 'class',
                            label: 'Class'
                        },
                        {
                            name: 'title',
                            label: 'Title'
                        }
                    ]
                }
            }
        });







        editor.on('asset:upload:response', function(response) {
            console.log('Upload successful', response);
        });
    </script>

</body>

</html>