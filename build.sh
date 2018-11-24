content_to_zip=(upload install.xml)
modules_path=()

zip_files () {
  for path in "${modules_path[@]}"
  do
    cd "$path"
    zip "$zip_name"  -r ./*
    cd -
  done
}

# API Build
zip_name="pagar_me.ocmod.zip"
modules_path=(API/2.3 API/2.x)

zip_files

# Checkout Build
zip_name="pagar_me_checkout.ocmod.zip"
modules_path=("Checkout Pagar.Me/2.3" "Checkout Pagar.Me/2.x")

zip_files
