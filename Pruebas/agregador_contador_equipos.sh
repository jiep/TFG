awk -F "\"*,\"*" '{print $1}' "./datos/2008-09.txt" "./datos/2009-10.txt" "./datos/2011-12.txt" "./datos/2012-13.txt" "./datos/2013-14.txt"  >> equipos.txt #Guarda la primera columna de cada archivo en equipos012-2013.txt

sort equipos.txt | uniq -c | wc -l # Cuenta el número de equipos totales
