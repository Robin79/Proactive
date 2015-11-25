#!/usr/bin/octave -qf

 arg_list = argv();

 p=size(arg_list);
 if p(1,1)~=5
 
   disp("<nsample><width><height><rit><filename>");   
 
 else
 
 nsample  = str2num(arg_list{1}); 
 width    = str2num(arg_list{2});
 heigth   = str2num(arg_list{3});
 rit      = str2num(arg_list{4});
 filename = arg_list{5};

# start = floor((nsample/2) - (width/2));
# stop = floor((nsample/2) + (width/2));

# signal = ones(nsample,1)*(heigth/2);
# signal(start:stop,1) = heigth;
  
  N=heigth;
  fi=(0:(pi/2)/nsample:(pi/2)-((pi/2)/nsample))+.0005;
  if(rit==1)
    signal=(abs(exp(-j*pi*(fi+pi/2)*(N-1)).*sin(pi*(fi+pi/2)*N)./sin(pi*(fi+pi/2)))-3);
  else
    signal=(abs(exp(-j*pi*(fi)*(N-1)).*sin(pi*(fi)*N)./sin(pi*(fi)))-3);  
  end
  #signal = (heigth*(rand(1,nsample)-.5));

 fileID = fopen(filename,'w');
 fwrite(fileID,signal,'int32');
 fclose(fileID);
 
 end
