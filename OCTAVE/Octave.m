#!/usr/bin/octave -qf

pkg load signal

set(0,'DefaultFigureVisible','off');

arg_list = argv();

disp(arg_list{1});
disp(arg_list{2});
disp(arg_list{3});

filenameS1 = sprintf('%s',arg_list{1});
filenameS2 = sprintf('%s',arg_list{2});
filenameOut = sprintf('%s',arg_list{3});

fileID = fopen(filenameS1);
signal1 = fread(fileID,'uint32');
fclose(fileID);

fileID = fopen(filenameS2);
signal2 = fread(fileID,'uint32');
fclose(fileID);

corrS1S2 = xcorr(signal1,signal2);

fileID = fopen(filenameOut,'wb');
fwrite(fileID,corrS1S2,'float');
fclose(fileID);

plot(corrS1S2,"-");
print -djpg ./IMAGES/myCorr2.jpg
