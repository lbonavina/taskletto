; Taskletto — Custom NSIS installer script

!macro customHeader
  !define MUI_WELCOMEPAGE_TITLE     "Bem-vindo ao Taskletto"
  !define MUI_WELCOMEPAGE_TEXT      "Este assistente irá guiá-lo pela instalação do Taskletto.$\r$\n$\r$\nTaskletto é um gerenciador de tarefas e notas com integração Git. Seus dados ficam no seu computador.$\r$\n$\r$\nClique em Próximo para continuar."
  !define MUI_FINISHPAGE_TITLE      "Instalação concluída!"
  !define MUI_FINISHPAGE_TEXT       "O Taskletto foi instalado com sucesso e está pronto para uso."
  !define MUI_FINISHPAGE_RUN        "$INSTDIR\Taskletto.exe"
  !define MUI_FINISHPAGE_RUN_TEXT   "Iniciar o Taskletto agora"
!macroend

!macro customWelcomePage
  !insertmacro MUI_PAGE_WELCOME
!macroend

!macro customInstall
  MessageBox MB_OKCANCEL|MB_ICONINFORMATION \
    "O Taskletto será instalado em:$\r$\n$\r$\n$INSTDIR$\r$\n$\r$\nClique em OK para confirmar." \
    IDOK taskletto_confirmed
  Abort
  taskletto_confirmed:

  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" "DisplayName"    "Taskletto ${VERSION}"
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" "DisplayVersion" "${VERSION}"
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" "Publisher"      "Lucas Bonavina"
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Uninstall\${APP_GUID}" "URLInfoAbout"   "https://github.com/lbonavina/taskletto"
!macroend

!macro customUnInstall
  MessageBox MB_YESNO|MB_ICONQUESTION \
    "Deseja remover também os dados do Taskletto (tarefas, notas e configurações)?$\r$\n$\r$\nClique em Não para manter seus dados." \
    IDNO taskletto_keep_data
  RMDir /r "$APPDATA\Taskletto"
  RMDir /r "$LOCALAPPDATA\Taskletto"
  taskletto_keep_data:
!macroend