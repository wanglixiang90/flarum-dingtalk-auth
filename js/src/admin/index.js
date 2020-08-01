import app from 'flarum/app';
import DingtalkSettingsModal from './components/DingtalkSettingsModal';

app.initializers.add('halobear-dingtalk-auth', () => {
    app.extensionSettings['halobear-dingtalk-auth'] = () => app.modal.show(new DingtalkSettingsModal());
});
