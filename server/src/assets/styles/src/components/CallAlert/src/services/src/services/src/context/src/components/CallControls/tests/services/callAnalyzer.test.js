import CallAnalyzer from '../../src/services/callAnalyzer';

describe('CallAnalyzer', () => {
  let analyzer;
  
  beforeEach(() => {
    analyzer = new CallAnalyzer();
  });
  
  describe('startCall', () => {
    it('should initialize a new call correctly', () => {
      const callId = 'test-call';
      const callState = analyzer.startCall(callId);
      
      expect(callState).toEqual({
        id: callId,
        transcription: '',
        protocolMentioned: false,
        closingDetected: false,
        sentimentScore: 0,
        startTime: expect.any(Date),
        endTime: null,
        status: 'active',
        alerts: []
      });
      
      expect(analyzer.activeCalls.has(callId)).toBe(true);
    });
  });
  
  describe('processTranscription', () => {
    it('should detect protocol number', () => {
      const callId = 'test-call';
      analyzer.startCall(callId);
      
      const result = analyzer.processTranscription(
        callId, 
        'Seu protocolo é 123456'
      );
      
      expect(result).toEqual({
        type: 'success',
        code: 'protocol_provided',
        title: 'Protocolo Registrado',
        message: 'O número de protocolo foi informado ao cliente.',
        callId: 'test-call',
        timestamp: expect.any(Date)
      });
      
      const callState = analyzer.activeCalls.get(callId);
      expect(callState.protocolMentioned).toBe(true);
    });
    
    it('should detect closing without protocol', () => {
      const callId = 'test-call';
      analyzer.startCall(callId);
      
      const result = analyzer.processTranscription(
        callId, 
        'Então é isso, obrigado!'
      );
      
      expect(result).toEqual({
        type: 'warning',
        code: 'missing_protocol',
        title: 'Protocolo Pendente',
        message: 'Lembre-se de informar o número de protocolo antes de encerrar.',
        callId: 'test-call',
        timestamp: expect.any(Date)
      });
      
      const callState = analyzer.activeCalls.get(callId);
      expect(callState.closingDetected).toBe(true);
    });
    
    it('should detect negative sentiment', () => {
      const callId = 'test-call';
      analyzer.startCall(callId);
      
      const result = analyzer.processTranscription(
        callId, 
        'Estou extremamente irritado com esse serviço horrível!'
      );
      
      expect(result).toEqual({
        type: 'error',
        code: 'negative_sentiment',
        title: 'Cliente Insatisfeito',
        message: 'A conversa apresenta sinais de insatisfação. Considere acionar um supervisor.',
        callId: 'test-call',
        timestamp: expect.any(Date)
      });
    });
  });
});