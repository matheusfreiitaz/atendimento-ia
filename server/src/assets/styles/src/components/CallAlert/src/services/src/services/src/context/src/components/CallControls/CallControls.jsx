import { useCallContext } from '../../context/CallContext';
import Button from '../Button';
import Icon from '../Icon';
import styles from './CallControls.module.scss';

const CallControls = () => {
  const { 
    isActive, 
    startCall, 
    endCall 
  } = useCallContext();

  const handleStartRealCall = () => startCall(false);
  const handleStartMockCall = () => startCall(true);
  const handleEndCall = () => endCall();

  return (
    <div className={styles.controlsContainer}>
      {!isActive ? (
        <div className={styles.startButtons}>
          <Button
            variant="primary"
            onClick={handleStartRealCall}
            icon="call"
            iconPosition="left"
          >
            Iniciar Chamada Real
          </Button>
          <Button
            variant="secondary"
            onClick={handleStartMockCall}
            icon="smart_toy"
            iconPosition="left"
          >
            Simular Chamada
          </Button>
        </div>
      ) : (
        <Button
          variant="danger"
          onClick={handleEndCall}
          icon="call_end"
          iconPosition="left"
        >
          Encerrar Chamada
        </Button>
      )}
      
      <div className={styles.quickActions}>
        <Button variant="icon" aria-label="Ajustes">
          <Icon name="settings" />
        </Button>
        <Button variant="icon" aria-label="Ajuda">
          <Icon name="help" />
        </Button>
      </div>
    </div>
  );
};

export default CallControls;