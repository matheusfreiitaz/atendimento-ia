import natural from 'natural';
import { PROTOCOL_PATTERNS, CLOSING_PHRASES } from '../utils/constants';

const tokenizer = new natural.WordTokenizer();
const analyzer = new natural.SentimentAnalyzer('Portuguese', natural.PorterStemmer, 'afinn');

class CallAnalyzer {
  constructor() {
    this.activeCalls = new Map();
    this.protocolPatterns = PROTOCOL_PATTERNS.map(pattern => new RegExp(pattern, 'i'));
    this.closingPatterns = CLOSING_PHRASES.map(phrase => new RegExp(phrase, 'i'));
  }

  /**
   * Inicia uma nova chamada
   * @param {string} callId - ID único da chamada
   * @returns {CallState} Objeto com estado inicial da chamada
   */
  startCall(callId) {
    const callState = {
      id: callId,
      transcription: '',
      protocolMentioned: false,
      closingDetected: false,
      sentimentScore: 0,
      startTime: new Date(),
      endTime: null,
      status: 'active',
      alerts: []
    };
    
    this.activeCalls.set(callId, callState);
    return callState;
  }

  /**
   * Processa um trecho de transcrição
   * @param {string} callId - ID da chamada
   * @param {string} text - Texto transcrito
   * @returns {object|null} Resultado da análise ou null se não houver alertas
   */
  processTranscription(callId, text) {
    const callState = this.activeCalls.get(callId);
    if (!callState) return null;

    // Atualiza transcrição
    callState.transcription += ' ' + text;
    
    // Analisa sentimento
    callState.sentimentScore = this.analyzeSentiment(text);
    
    // Verifica protocolo
    const protocolCheck = this.checkProtocol(text, callState);
    if (protocolCheck) return protocolCheck;
    
    // Verifica encerramento
    const closingCheck = this.checkClosing(text, callState);
    if (closingCheck) return closingCheck;
    
    // Verifica sentimento negativo
    const sentimentCheck = this.checkSentiment(callState);
    if (sentimentCheck) return sentimentCheck;
    
    return null;
  }

  analyzeSentiment(text) {
    const tokens = tokenizer.tokenize(text.toLowerCase());
    return analyzer.getSentiment(tokens);
  }

  checkProtocol(text, callState) {
    if (callState.protocolMentioned) return null;
    
    const protocolMatch = this.protocolPatterns.some(pattern => pattern.test(text));
    if (protocolMatch) {
      callState.protocolMentioned = true;
      
      // Remove alertas de protocolo pendente
      callState.alerts = callState.alerts.filter(a => a.code !== 'missing_protocol');
      
      return {
        type: 'success',
        code: 'protocol_provided',
        title: 'Protocolo Registrado',
        message: 'O número de protocolo foi informado ao cliente.',
        callId: callState.id,
        timestamp: new Date()
      };
    }
    
    return null;
  }

  checkClosing(text, callState) {
    if (callState.closingDetected) return null;
    
    const isClosing = this.closingPatterns.some(pattern => pattern.test(text)) || 
                     this.isClosingSentence(text);
    
    if (isClosing) {
      callState.closingDetected = true;
      
      if (!callState.protocolMentioned) {
        const alert = {
          type: 'warning',
          code: 'missing_protocol',
          title: 'Protocolo Pendente',
          message: 'Lembre-se de informar o número de protocolo antes de encerrar.',
          callId: callState.id,
          timestamp: new Date()
        };
        
        callState.alerts.push(alert);
        return alert;
      }
    }
    
    return null;
  }

  checkSentiment(callState) {
    if (callState.sentimentScore < -0.5) {
      const hasAlert = callState.alerts.some(a => a.code === 'negative_sentiment');
      
      if (!hasAlert) {
        const alert = {
          type: 'error',
          code: 'negative_sentiment',
          title: 'Cliente Insatisfeito',
          message: 'A conversa apresenta sinais de insatisfação. Considere acionar um supervisor.',
          callId: callState.id,
          timestamp: new Date()
        };
        
        callState.alerts.push(alert);
        return alert;
      }
    }
    
    return null;
  }

  isClosingSentence(text) {
    const doc = nlp(text.toLowerCase());
    const tokens = tokenizer.tokenize(text);
    const lastToken = tokens[tokens.length - 1];
    
    // Verifica despedidas explícitas
    const goodbyeWords = ['tchau', 'adeus', 'até', 'logo', 'obrigado'];
    if (goodbyeWords.some(word => tokens.includes(word))) return true;
    
    // Verifica perguntas finais
    if (lastToken === '?' && 
        (tokens.includes('mais') || tokens.includes('dúvida'))) {
      return true;
    }
    
    // Verifica agradecimento final
    if (text.includes('obrigado') && text.length < 50) return true;
    
    return false;
  }

  endCall(callId) {
    const callState = this.activeCalls.get(callId);
    if (!callState) return null;
    
    callState.endTime = new Date();
    callState.status = 'completed';
    
    this.activeCalls.delete(callId);
    return callState;
  }
}

export default new CallAnalyzer();