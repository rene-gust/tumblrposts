- generate ssl key and certificate with ./generate-cert.sh
- import nginx.crt in your browser as a new authority
- for lunix users:
  - `certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n sc.local.pttde.de -i nginx.crt`
    - wobei sc.local.pttde.de die lokale domain ist womit man arbeiten möchte
    - und nginx.crt das Zertifikat ist
- or try this: https://github.com/FiloSottile/mkcert