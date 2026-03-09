; ─────────────────────────────────────────────────────────────────────────────
; Taskletto — Custom NSIS installer script
; ─────────────────────────────────────────────────────────────────────────────

; ── Página de boas-vindas ────────────────────────────────────────────────────
!macro customWelcomePage
  !define MUI_WELCOMEPAGE_TITLE "Bem-vindo ao Taskletto ${VERSION}"
  !define MUI_WELCOMEPAGE_TEXT "Este assistente irá guiá-lo pela instalação do Taskletto $\r$\n$\r$\nTaskletto é um gerenciador de tarefas e notas offline, rápido e sem assinatura. Seus dados ficam no seu computador.$\r$\n$\r$\nClique em Próximo para continuar."
  !insertmacro MUI_PAGE_WELCOME
!macroend

; ── Página de conclusão ──────────────────────────────────────────────────────
!macro customFinishPage
  !define MUI_FINISHPAGE_TITLE "Instalação concluída!"
  !define MUI_FINISHPAGE_TEXT "O Taskletto foi instalado com sucesso.$\r$\n$\r$\nClique em Concluir para fechar este assistente."
  !define MUI_FINISHPAGE_RUN "$INSTDIR\Taskletto.exe"
  !define MUI_FINISHPAGE_RUN_TEXT "Iniciar o Taskletto agora"
  !define MUI_FINISHPAGE_SHOWREADME ""
  !define MUI_FINISHPAGE_SHOWREADME_NOTCHECKED
  !insertmacro MUI_PAGE_FINISH
!macroend

; ── Ações pós-instalação ─────────────────────────────────────────────────────
!macro customInstall
  ; Registrar no Add/Remove Programs com informações extras
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "DisplayName" "Taskletto ${VERSION}"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "DisplayVersion" "${VERSION}"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "Publisher" "Lucas Bonavina"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "URLInfoAbout" "https://github.com/lbonavina/taskletto"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "HelpLink" "https://github.com/lbonavina/taskletto/issues"
  WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" \
    "Comments" "Gerenciador de tarefas e notas offline"
!macroend

; ── Ações pré-desinstalação ──────────────────────────────────────────────────
!macro customUnInstall
  ; Perguntar se quer apagar os dados do usuário
  MessageBox MB_YESNO|MB_ICONQUESTION \
    "Deseja remover também os dados do Taskletto (tarefas, notas e configurações)?$\r$\n$\r$\nSe clicar em Não, seus dados serão mantidos." \
    IDNO keep_data

  ; Apagar dados do AppData
  RMDir /r "$APPDATA\Taskletto"
  RMDir /r "$LOCALAPPDATA\Taskletto"

  keep_data:
!macroend
