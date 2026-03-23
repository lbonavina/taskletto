/**
 * electron-builder.mjs
 *
 * NativePHP Desktop v2 detecta este arquivo na raiz do projeto Laravel
 * e mescla as configurações aqui com as configurações internas dele.
 * É aqui que ficam as configs customizadas de NSIS, assets do instalador, etc.
 *
 * IMPORTANTE: caminhos são relativos ao diretório onde o electron-builder roda
 * (dentro de vendor/nativephp/electron/). Por isso os assets do instalador
 * precisam ser copiados para lá via prebuild (já configurado em config/nativephp.php).
 */

export default {
    appId: 'com.lbonavina.taskletto',
    productName: 'Taskletto',
    copyright: '© 2025 Lucas Bonavina',

    win: {
        icon: 'build/icon.ico',
        target: [
            {
                target: 'nsis',
                arch: ['x64'],
            },
        ],
        publisherName: 'Lucas Bonavina',
        verifyUpdateCodeSignature: false,
    },

    nsis: {
        // Instalador customizado (não one-click) com seleção de diretório
        oneClick: false,
        perMachine: false,
        allowElevation: true,
        allowToChangeInstallationDirectory: true,

        // Ícones
        installerIcon: 'build/icon.ico',
        uninstallerIcon: 'build/icon.ico',

        // Atalhos
        createDesktopShortcut: true,
        createStartMenuShortcut: true,
        menuCategory: 'Taskletto',
        shortcutName: 'Taskletto',

        // Assets customizados copiados pelo prebuild
        license: 'build/LICENSE.rtf',
        installerHeader: 'build/installerHeader.bmp',
        installerSidebar: 'build/installerSidebar.bmp',
        include: 'build/installer.nsh',

        // Localização PT-BR
        displayLanguageSelector: false,
        language: '1046',

        // Comportamento pós-instalação
        runAfterFinish: true,
        deleteAppDataOnUninstall: false,
        uninstallDisplayName: 'Taskletto ${version}',
        artifactName: 'Taskletto-Setup-${version}.${ext}',
    },

    mac: {
        icon: 'build/icon.ico',
        category: 'public.app-category.productivity',
        target: ['dmg'],
    },

    linux: {
        icon: 'build/icon.ico',
        target: ['AppImage'],
        category: 'Office',
    },
};
