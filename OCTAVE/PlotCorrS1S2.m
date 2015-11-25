pkg load signal 
addpath('./OCTAVE');

args = argv;

fnameS1          = args{1}; 
fnameS2          = args{2};
fnameCorrS1S2    = args{3};
fnameImgCorrS1S2 = args{4};
nsamples         = str2num(args{5});
fsampling        = str2num(args{6});

printf("\n\r fnameS1 : %s",fnameS1);
printf("\n\r fnameS2 : %s",fnameS2);
printf("\n\r fnameCorrs1S2    : %s",fnameCorrS1S2);
printf("\n\r fnameImgCorrS1S2 : %s",fnameImgCorrS1S2);
printf("\n\r nsamples: %d",nsamples);

%%% Leggo il primo file di segnale
fileID  = fopen(fnameS1);
signal1 = fread(fileID,'int8');
fclose(fileID);

%%% Leggo il secondoo file di segnale
fileID  = fopen(fnameS2);
signal2 = fread(fileID,'int8');
fclose(fileID);

dt=1/fsampling;
max_lag = round(5/dt);

%[vcorrS1S2,delaycorrS1S2] = xcorr(signal1,signal2,max_lag,'coeff');
[vcorrS1S2,delaycorrS1S2] = xcorr(signal1,signal2);

%[xcorr_max,xcorr_index]=max(vcorrS1S2);

%delta_T = delaycorrS1S2(xcorr_index)*dt;

fileID = fopen(fnameCorrS1S2,'wb');
fwrite(fileID,vcorrS1S2,'float');
fclose(fileID);


%% Calcola il massimo e il minimo  e normalizza vcorrS1S2 %% 

[r3,c3] = size(vcorrS1S2);
nsamples           = r3; 
index_samples      = (1:nsamples);

[MaxS1,IndexMaxS1] = max(vcorrS1S2);
[MinS1,IndexMinS1] = min(vcorrS1S2);
vcorrS1S2          = vcorrS1S2 ./ (MaxS1 - MinS1);
MaxS1 		   = MaxS1 / (MaxS1 - MinS1);
MinS1              = MinS1 / (MaxS1 - MinS1);

VectXlim           = [0,nsamples];
VectYlim           = [MinS1,MaxS1*1.2];
VectYTick          = [-1,-0.5,0,0.5,1];
Ymiddle            = floor((VectXlim(1,1)+ VectXlim(1,2))/2);
VectXTick          = [VectXlim(1,1),Ymiddle,VectXlim(1,2)]; 


set(0,'DefaultFigureVisible','off');

b1 = figure('PaperSize',[20.98 29.68]);

h = axes();
hold off;

H1 = plot(vcorrS1S2);
%set(H1,'LineStyle','none');
hold on;

%H2 = area(index_samples,vcorrS1S2);
%set(H2,'FaceColor','yellow');
%set(H2,'LineStyle','none');

%ST1 = stem(IndexMaxS1,MaxS1);

%set(ST1,'MarkerFaceColor','red');
%set(ST1,'MarkerEdgeColor','blue');

%metodo alternativo allo STEM 
%plot([IndexMaxS1,IndexMaxS1],[0,MaxS1],'b');
%plot(IndexMaxS1,MaxS1,'*r');

%set(h,'Color','blue');
set(h,'LineWidth',0.05);
set(h,'XAxisLocation','bottom');
set(h,'YAxisLocation','left');
set(h,'Xdir','normal');
set(h,'XlimMode','manual');
set(h,'XlimMode','manual');
set(h,'Xgrid','on');
set(h,'Ygrid','on');
set(h,'Xlim',VectXlim);
set(h,'Ylim',VectYlim);
set(h,'XTickMode','manual');
set(h,'XTick',VectXTick);
set(h,'XTicklabel',(VectXTick .- floor(nsamples / 2)) ./fsampling);
set(h,'YTickMode','manual');
set(h,'YTick',VectYTick);
set(h,'TickDir','out');
set(h,'TickLength',[0.02,0.02]);


xlabel('TIME[s]','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);
ylabel('CORRELATION','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);

msgR1 = sprintf("   Signal1  : %s  n = %d fs=%d [Hz]", fnameS1,nsamples/2,fsampling);
msgR2 = sprintf("   Signal2  : %s  n = %d fs=%d [Hz]", fnameS2,nsamples/2,fsampling);
msgR3 = sprintf("   Max : [%fs,%f]",delaycorrS1S2(IndexMaxS1)/fsampling,MaxS1);

#delaycorrS1S2(IndexMaxS1)
fileID = fopen('MaxDelay.dat','wb');
fwrite(fileID,delaycorrS1S2(IndexMaxS1)/fsampling,'float');
fclose(fileID);

text (5, 1.15, msgR1);
text (5, 1.1, msgR2);
text (5, 1.05, msgR3);


print -djpg ImgCorrS1S2;

%% Richiamo calcolo script Coerenza
%% coherenceStandard, fft_signal1, fft_signal2, freqAx
Coherence

b1 = figure('PaperSize',[20.98 29.68]);
plot(freqAx,coherenceStandard,'-g');
hold on
plot(freqAx,fft_signal1,'-r');
plot(freqAx,fft_signal2,'-b');

hold off
xlabel('Frequency [Hz]');
ylabel('Amplitude []');
print -djpg COHER.jpg;
