import SettingsModal from 'flarum/components/SettingsModal';

export default class DingtalkSettingsModal extends SettingsModal {
    className() {
        return 'AuthDingtalkSettingsModal Modal--small';
    }

    title() {
        return app.translator.trans('dingtalk-auth.admin.settings.title');
    }

    form() {
        return [
            <div className="Form-group">
                <label>{app.translator.trans('dingtalk-auth.admin.settings.api_corp_id')}</label>
                <input className="FormControl" bidi={this.setting('dingtalk-auth.corp_id')}/>
            </div>,
            <div className="Form-group">
                <label>{app.translator.trans('dingtalk-auth.admin.settings.api_app_key')}</label>
                <input className="FormControl" bidi={this.setting('dingtalk-auth.app_key')}/>
            </div>,
            <div className="Form-group">
                <label>{app.translator.trans('dingtalk-auth.admin.settings.api_app_secret')}</label>
                <input className="FormControl" bidi={this.setting('dingtalk-auth.app_secret')}/>
            </div>,
        ];
    }
}
