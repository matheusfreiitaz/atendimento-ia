import { useEffect, useState } from 'react';
import styles from './CallAlert.module.scss';
import { useCallContext } from '../../context/CallContext';
import Icon from '../Icon';

const CallAlert = () => {
  const { alert } = useCallContext();
  const [isVisible, setIsVisible] = useState(false);
  const [isExiting, setIsExiting] = useState(false);

  useEffect(() => {
    if (alert) {
      setIsVisible(true);
      setIsExiting(false);
      
      const timer = setTimeout(() => {
        setIsExiting(true);
        setTimeout(() => setIsVisible(false), 300);
      }, 5000);
      
      return () => clearTimeout(timer);
    } else {
      setIsExiting(true);
      setTimeout(() => setIsVisible(false), 300);
    }
  }, [alert]);

  if (!isVisible || !alert) return null;

  const alertType = {
    warning: {
      icon: 'warning',
      color: 'var(--color-warning)'
    },
    success: {
      icon: 'check_circle',
      color: 'var(--color-success)'
    },
    error: {
      icon: 'error',
      color: 'var(--color-danger)'
    }
  }[alert.type] || alertType.warning;

  return (
    <div 
      className={`${styles.alert} ${styles[alert.type]} ${isExiting ? styles.exit : ''}`}
      role="alert"
      aria-live="assertive"
    >
      <div className={styles.iconContainer}>
        <Icon name={alertType.icon} color={alertType.color} size={24} />
      </div>
      <div className={styles.content}>
        <h3 className={styles.title}>{alert.title}</h3>
        <p className={styles.message}>{alert.message}</p>
      </div>
      <button 
        className={styles.closeButton}
        onClick={() => setIsExiting(true)}
        aria-label="Fechar alerta"
      >
        <Icon name="close" size={18} />
      </button>
    </div>
  );
};

export default CallAlert;