pkg load signal 

args = argv;

fnameS1          = args{1}; 
L                = str2num(args{2});
Fs               = str2num(args{3});

fileID  = fopen(fnameS1);
signal1 = fread(fileID,'int32');
fclose(fileID);

T = 1/Fs;
t= (0:L-1)*T;

Y = fft(signal1,2048);
f = Fs/2*linspace(0,1,1025);

set(0,'DefaultFigureVisible','off');

b1 = figure('PaperSize',[20.98 29.68]);

h = axes();
hold off;

H1 = plot(f,2*abs(Y(1:1025)));

hold on;

set(h,'LineWidth',0.05);
set(h,'XAxisLocation','bottom');
set(h,'YAxisLocation','left');
set(h,'Xdir','normal');
set(h,'XlimMode','manual');
set(h,'XlimMode','manual');
set(h,'Xgrid','on');
set(h,'Ygrid','on');
%set(h,'Xlim',VectXlim);
%set(h,'Ylim',VectYlim);
set(h,'XTickMode','manual');
%set(h,'XTick',VectXTick);
%set(h,'XTicklabel',(VectXTick .- floor(nsamples / 2)) ./fsampling);
set(h,'YTickMode','manual');
%set(h,'YTick',VectYTick);
set(h,'TickDir','out');
set(h,'TickLength',[0.02,0.02]);


xlabel('FREQUENCY[Hz]','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);
ylabel('|Y(f)|','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);

print -djpg fft_dati.jpg;

