SOURCE_FOLDER="/C/Users/USUARIO/Documents/proyecto-pos/app-pos-laravel"
DESTINATION_FOLDER="/C/xampp/htdocs/app-pos-laravel"

if [ -d "$DESTINATION_FOLDER" ]; then
    rm -rf "$DESTINATION_FOLDER"
fi

cp -r "$SOURCE_FOLDER" "$DESTINATION_FOLDER"

echo "Copy in XAMPP"
