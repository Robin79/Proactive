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

 set(0,'DefaultFigureVisible','off');

 b1 = figure('PaperSize',[20.98 29.68]);

 h = axes();
 hold off;

 H1 = plot(t,signal1);
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


 xlabel('TIME[s]','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);
 ylabel('DB','LineWidth',1,'FontName','Arial Narrow','Color',[1 0 0]);

 print -djpg dati.jpg;

