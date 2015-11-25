
%- INPUT ------------------------------------------------------------------
%fs = 5000;                                                                  % Sampling frequency [Hz]
fs = fsampling;
win = 1;                                                                    % Averaging window [s]
overlap = 50;                                                               % Window overlap [%]
DATA = [signal1 signal2];                                                   % Signals from sensors
%--------------------------------------------------------------------------

dt = 1/fs;                                                                  % Sampling interval [s]
ns = size(DATA,1);                                                          % Total number of samples [-]
winSample = 2^(floor(log2(win/dt)));                                        % Averaging window [samples]
overlapSample = (overlap/100)*winSample;                                    % Window overlap [samples]
df = fs/winSample;                                                          % Frequency step [Hz]
freqAx = 0:df:(winSample/2-1)*df;                                           % Frequency axis till Nyquist[Hz]
% Standard Coherence computation
winCounter = 1;
hamWin = [hamming(winSample) hamming(winSample)];                           % Hamming window
for I = 1:(winSample-overlapSample):ns-winSample
    dataTemp = DATA(I:I+winSample-1,:).*hamWin;                             % Data windowind with Hamming window
    dataFreqTemp = fft(dataTemp,winSample,1);                               % Fourier Transform
    dataFreqTemp(winSample/2+1:winSample,:) = [];                           % Discard frequencies above Nyquist
    autoSpectra1(:,winCounter) = dataFreqTemp(:,1).*conj(dataFreqTemp(:,1));   % Auto spectra of signal 1
    autoSpectra2(:,winCounter) = dataFreqTemp(:,2).*conj(dataFreqTemp(:,2));   % Auto spectra of signal 2
    xSpectra(:,winCounter) = conj(dataFreqTemp(:,1)).*dataFreqTemp(:,2);   % Cross spectra of signals 1 & 2
    winCounter = winCounter +1;
end
avSpectra1 = mean(autoSpectra1,2);
avSpectra2 = mean(autoSpectra2,2);
avXSpectra = mean(xSpectra,2);
coherenceStandard = (abs(avXSpectra).^2)./(avSpectra1.*avSpectra2);
% % Coherence estimate using Welch's averaged periodogram method
% [coherence_welch,F] = mscohere(DATA(:,1),DATA(:,2),hamming(winSample),overlapSample,winSample,fs);

fft_signal1_intera = abs(fft(signal1,winSample));
fft_signal1 = fft_signal1_intera(1:winSample/2)./max(fft_signal1_intera(1:winSample/2));
fft_signal2_intera = abs(fft(signal2,winSample));
fft_signal2 = fft_signal2_intera(1:winSample/2)./max(fft_signal2_intera(1:winSample/2));

%- Plot -------------------------------------------------------------------
%figure
%plot(freqAx,coherenceStandard,'r')
% hold on
% plot(F,coherence_welch,'k')
% hold off
%set(gca,'xlim',[0 max(freqAx)],'ylim',[0 1])
%xlabel('Frequency [kHz]'); ylabel('Coherence [-]');
