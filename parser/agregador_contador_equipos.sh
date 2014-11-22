awk -F "\"*,\"*" '{print $1}' 2012-2013.txt > equipos012-2013.txt #Guarda la primera columna de cada archivo en equipos012-2013.txt

sort equipos2013-2014.txt | uniq -c | wc -l # Cuenta el número de equipos totales
