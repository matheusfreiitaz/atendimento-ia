import { createContext, useContext, useState, useEffect } from 'react';
import callAnalyzer from '../services/callAnalyzer';
import { runMockCall } from '../services/mockService';

const CallContext = createContext();

export const CallProvider = ({ children }) => {
  const [currentCall, setCurrentCall] = useState(null);
  const [alert, setAlert] = useState(null);
  const [isActive, setIsActive] = useState(false);
  const [transcription, setTranscription] = useState('');
  const [callsHistory, setCallsHistory] = useState([]);

  const startCall = async (mock = false) => {
    const callId = `call-${Date.now()}`;
    const newCall = callAnalyzer.startCall(callId);
    
    setCurrentCall(newCall);
    setIsActive(true);
    setTranscription('');
    setAlert(null);
    
    if (mock) {
      await runMockCall(callAnalyzer, callId);
      endCall();
    }
  };

  const processTranscription = (text) => {
    if (!currentCall) return;
    
    setTranscription(prev => prev + ' ' + text);
    const result = callAnalyzer.processTranscription(currentCall.id, text);
    
    if (result) {
      setAlert(result);
    }
  };

  const endCall = () => {
    if (!currentCall) return;
    
    const endedCall = callAnalyzer.endCall(currentCall.id);
    setCallsHistory(prev => [endedCall, ...prev]);
    setCurrentCall(null);
    setIsActive(false);
  };

  const dismissAlert = () => {
    setAlert(null);
  };

  return (
    <CallContext.Provider
      value={{
        currentCall,
        alert,
        isActive,
        transcription,
        callsHistory,
        startCall,
        endCall,
        processTranscription,
        dismissAlert
      }}
    >
      {children}
    </CallContext.Provider>
  );
};

export const useCallContext = () => useContext(CallContext);