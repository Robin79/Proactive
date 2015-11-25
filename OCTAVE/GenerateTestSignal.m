#!/usr/bin/octave -qf

 arg_list = argv();

 p=size(arg_list);
 if p(1,1)~=4
 
   disp("<nsample><width><height><filename>");   
 
 else
 
 nsample  = str2num(arg_list{1}); 
 width    = str2num(arg_list{2});
 heigth   = str2num(arg_list{3});
 filename = arg_list{4};
# disp(nsample);
# disp(width);
# disp(heigth);

# start = floor((nsample/2) - (width/2));
# stop = floor((nsample/2) + (width/2));

# signal = ones(nsample,1)*(heigth/2);
# signal(start:stop,1) = heigth;
  
  signal = (heigth*(rand(1,nsample)-.5));

 fileID = fopen(filename,'w');
 fwrite(fileID,signal,'int32');
 fclose(fileID);
 
 end
