#! /usr/bin/sh

docker container run -v ~/Documents/Alizon/SAE/html/commande/work:/work bigpapoo/html2pdf
mv test.html work/
cp script_conversion.sh work/
# sh work/script_conversion.sh FAUT FAIRE PAR LE CONTAINEUR
cp work/result.pdf ./
