pkg load signal

args = argv;

fnameS1 = args{1};
L = str2num(args{2});
Fs = str2num(args{3});

fileID  = fopen(fnameS1);
signal1 = fread(fileID,'int8');
fclose(fileID);

maxAbs = max(abs(signal1))*1.05;

dt = 1/Fs;
timeAx = (0:L-1)*dt;

set(0,'DefaultFigureVisible','off');

% b1 = figure('PaperSize',[20.98 29.68]);
figure('units','centimeters','position',[1 2 25 11])
h = axes('units','normalized','position',[.11 .15 .88 .8],'FontName','Arial','FontUnits','Points','FontSize',13);

% h = axes();
% hold off;

plot(timeAx,signal1);
% hold on;

% set(h,'LineWidth',0.05);
% set(h,'XAxisLocation','bottom');
% set(h,'YAxisLocation','left');
% set(h,'Xdir','normal');
% set(h,'XlimMode','manual');
% set(h,'XlimMode','manual');
% set(h,'Xgrid','on');
% set(h,'Ygrid','on');
% %set(h,'Xlim',VectXlim);
% %set(h,'Ylim',VectYlim);
% set(h,'XTickMode','manual');
% %set(h,'XTick',VectXTick);
% %set(h,'XTicklabel',(VectXTick .- floor(nsamples / 2)) ./fsampling);
% set(h,'YTickMode','manual');
% %set(h,'YTick',VectYTick);
% set(h,'TickDir','out');
% set(h,'TickLength',[0.02,0.02]);
set(h,'ylim',[-maxAbs maxAbs],'xlim',[min(timeAx) max(timeAx)])
set(h,'Xgrid','on','Ygrid','on');
set(h,'xtick',min(timeAx):10:max(timeAx),'TickDir','out','TickLength',[0.005 0.005],'box','on')

xlabel('Time[s]') %,'LineWidth',1,'FontName','Arial','Color',[0 0 0]);
ylabel('Amplitude [-]') %,'LineWidth',1,'FontName','Arial','Color',[0 0 0]);

print -djpg dati.jpg;

%%%rez = 300;                                                                 % resolution (dpi) of final graphic
%%%f = gcf;

%%%%%figpos = getpixelposition(f);
%%resolution = get(0,'ScreenPixelsPerInch');
%%%%%%set(f,'paperunits','inches','papersize',figpos(3:4)/resolution,'paperposition',[0 0 figpos(3:4)/resolution]);
% path = 'C:\Users\Diego\Desktop\';
%%name = 'dati';
%%%print(f,name,'-dpng',['-r',num2str(rez)],'-opengl')

% print(f,  fullfile(path,name),'-dpng',['-r',num2str(rez)],'-opengl')
