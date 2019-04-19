#lance le programme python d'analyse de l'image
cd  /home/fileapi
rm *
wget $1
mv * source.jpg
if [ -f /home/fileapi/source.jpg ]
then
cp -f /home/fileapi/source.jpg /home/workspace/models/research/object_detection/test_images/image2.jpg

#reduit � la taile � 200ko
mogrify -resize 200000@ /home/workspace/models/research/object_detection/test_images/image2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg  /var/www/html/data/sred_$2.jpg

#lance le ssd sur le fichier image2
cd /home/workspace/models/research/object_detection
python3  reco-michel.py
cp /home/fileapi/source.jpg /var/www/html/data/source_$2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg_detect.jpg /var/www/html/data/detect_$2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg_listbox.txt /var/www/html/data/list_zone_$2.txt

# � la fin , on a sur le repertoire /var/www/html , pour xxx le uuid du fichier :
# source_xxx.jpg l'image r�cup�r�e
# sred_xxx.jpg  l'image r�cup�r�e r�duite
# list_zone_xxx.txt qui contient l'ensemble des zones
# detect_xxx.jpg l'image avec les zones redessin�es, pour m�moire

echo 1
else
echo 0
fi
