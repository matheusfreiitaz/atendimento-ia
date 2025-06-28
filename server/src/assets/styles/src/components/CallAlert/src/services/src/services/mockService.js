export const generateMockCall = (callId) => ({
  id: callId || `mock-call-${Date.now()}`,
  transcription: '',
  protocolMentioned: false,
  closingDetected: false,
  sentimentScore: 0,
  startTime: new Date(),
  endTime: null,
  status: 'active',
  alerts: []
});

export const mockTranscriptions = [
  {
    input: "Bom dia, meu nome é Carlos e preciso de ajuda com minha conta.",
    output: {
      sentiment: -0.2,
      triggers: []
    }
  },
  {
    input: "Estou muito insatisfeito com o serviço!",
    output: {
      sentiment: -0.8,
      triggers: ['negative_sentiment']
    }
  },
  {
    input: "Entendi, obrigado pela ajuda. Qual é o número do protocolo?",
    output: {
      sentiment: 0.5,
      triggers: ['closing_detected']
    }
  },
  {
    input: "O protocolo é 123456",
    output: {
      sentiment: 0.3,
      triggers: ['protocol_provided']
    }
  },
  {
    input: "Então é isso, obrigado!",
    output: {
      sentiment: 0.7,
      triggers: ['closing_detected']
    }
  }
];

export const runMockCall = async (callAnalyzer, callId, delay = 1500) => {
  const call = callAnalyzer.startCall(callId);
  
  for (const item of mockTranscriptions) {
    await new Promise(resolve => setTimeout(resolve, delay));
    const result = callAnalyzer.processTranscription(callId, item.input);
    
    if (result) {
      console.log('Alerta gerado:', result);
      // Em uma aplicação real, você enviaria isso para o contexto/UI
    }
  }
  
  await new Promise(resolve => setTimeout(resolve, delay));
  const endedCall = callAnalyzer.endCall(callId);
  console.log('Chamada encerrada:', endedCall);
  
  return endedCall;
};