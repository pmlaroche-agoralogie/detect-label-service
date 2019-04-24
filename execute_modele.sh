#lance le programme python d'analyse de l'image
cd  /home/fileapi
rm *jpg
wget "$1"
cp -f /home/fileapi/*jpg /home/workspace/models/research/object_detection/test_images/image2.jpg

#reduit ˆ la taile ˆ 200ko
mogrify -resize 200000@ /home/workspace/models/research/object_detection/test_images/image2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg  /var/www/html/data/sred_$2.jpg

#lance le ssd sur le fichier image2
cd /home/workspace/models/research/object_detection
python3  reco-michel.py
cp /home/fileapi/*jpg /var/www/html/data/source_$2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg_detect.jpg /var/www/html/data/detect_$2.jpg
cp /home/workspace/models/research/object_detection/test_images/image2.jpg_listbox.txt /var/www/html/data/list_zone_$2.txt

# ˆ la fin , on a sur le repertoire /var/www/html , pour xxx le uuid du fichier :
# source_xxx.jpg l'image rŽcupŽrŽe
# sred_xxx.jpg  l'image rŽcupŽrŽe rŽduite
# list_zone_xxx.txt qui contient l'ensemble des zones
# detect_xxx.jpg l'image avec les zones redessinŽes, pour mŽmoire

echo 1
