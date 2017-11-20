import { Component } from '@angular/core';
import { GlobalState } from '../../../global.state';
import { Variable } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';

@Component({
  selector: 'ba-page-top',
  templateUrl: './baPageTop.html',
  styleUrls: ['./baPageTop.scss']
})
export class BaPageTop {

    isScrolled: boolean = false;
    isMenuCollapsed: boolean = false;

    protected authorizationService: IAuthorizationService;

    constructor(private _state: GlobalState, authorizationService: AuthorizationService) {
        this.authorizationService = authorizationService;
        this._state.subscribe('menu.isCollapsed', (isCollapsed) => {
            this.isMenuCollapsed = isCollapsed;
        });
    }

    public toggleMenu() {
        this.isMenuCollapsed = !this.isMenuCollapsed;
        this._state.notifyDataChanged('menu.isCollapsed', this.isMenuCollapsed);
        return false;
    }

    public scrolledChanged(isScrolled) {
        this.isScrolled = isScrolled;
    }

    isUserInfoDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.authorizationService.lastUser);
    }
    userAvatarUrl(): string {
        // TODO: replace with real user avatar
        const result: string = 'https://www.b1g1.com/assets/admin/images/no_image_user.png';
        return result;
    }
}
