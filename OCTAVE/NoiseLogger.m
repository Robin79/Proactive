 pkg load signal 

 args = argv;

 fnameS1          = args{1}; %Nome file contenente i dati 
 Win              = str2num(args{2}); %dimensione finestra [s]
 Fs               = str2num(args{3}); %frequenza di campionamento [Hz]

 %%% Leggo dati dal file
 fileID  = fopen(fnameS1);
 signal1 = fread(fileID,'int32');
 fclose(fileID);


 T = 1/Fs;
 ns = length(signal1);
 WinSample = round(Win*Fs);
 df = Fs/WinSample;
 
 minimi=[];
 freq_max=[];

 for i = 1:WinSample:ns-WinSample+1    

     Vett_temp = signal1(i:i+WinSample-1);
      minimi = [ minimi 20*log10(min(Vett_temp)) ];

     [val_max,ind_max] =  max(abs(fft(Vett_temp)));
      
     freq_max = [ freq_max (ind_max-1)*df ];

 end

 %int32(minimi)
 %signal1 

 fileID = fopen('./frequenze_max.dat','w');
 fwrite(fileID,int32(max(freq_max)),'int32');
 fclose(fileID);

 fileID = fopen('./minimi_min.dat','w');
 fwrite(fileID,int32(20*log10(mean(minimi))),'int32');
 fclose(fileID);

 fileID = fopen('./frequenze.dat','w');
 fwrite(fileID,int32(freq_max),'int32');
 fclose(fileID);

 fileID = fopen('./minimi.dat','w');
 fwrite(fileID,minimi,'int32');
 fclose(fileID);



