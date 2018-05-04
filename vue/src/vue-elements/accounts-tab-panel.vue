<template>
	<div class="tab-view">
		<div class="panel-body">
			<div class="toast  toast-warning" v-html="labels.twitter_warning" v-if="twitter_warning">
			
			</div>
			<div class="container">
				<div class="columns" :class="'rop-tab-state-'+is_loading">
					<div class="column col-sm-12 col-md-12 col-lg-12 text-left rop-available-accounts mt-2">
						<div class="empty mb-2" v-if="accountsCount === 0">
							<div class="empty-icon">
								<i class="fa fa-3x fa-user-circle-o"></i>
							</div>
							<p class="empty-title h5">{{labels.no_accounts}}</p>
							<p class="empty-subtitle">{{labels.no_accounts_desc}}</p>
						</div>
						<div class="account-container" v-for="( account, id ) in accounts">
							<service-user-tile :account_data="account" :account_id="id"></service-user-tile>
							<span class="divider"></span>
						</div>
						<div class="add-accounts">
							<add-account-tile></add-account-tile>
							<span class="divider"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer" v-if="accountsCount > 0">
				<div class="columns">
					<div class="column col-12">
						<p class="text-gray"><i class="fa fa-info-circle"></i> <span
								v-html="labels.has_accounts_desc"></span></p>
					</div>
				</div>
				<div class="column col-12 text-right">
					<button class="btn btn-secondary" @click="resetAccountData()">
						<i class="fa fa-ban" v-if="!this.is_loading"></i>
						<i class="fa fa-spinner fa-spin" v-else></i>
						{{labels.remove_all_cta}}
					</button>
				</div>
			</div>
		</div>
	
	</div>
</template>

<script>
	import SignInBtn from './sign-in-btn.vue'
	import ServiceUserTile from './service-user-tile.vue'
	import AddAccountTile from './reusables/add-account-tile.vue'

	module.exports = {
		name: 'account-view',
		data: function () {
			return {
				addAccountActive: false,
				accountsCount: 0,
				is_loading: false,
				twitter_warning: false,
				labels: this.$store.state.labels.accounts,
				upsell_link: ropApiSettings.upsell_link,
			}
		},
		computed: {
			/**
			 * Get all the available/active accounts.
			 */
			accounts: function () {
				const all_accounts = {};
				let twitter = 0;
				const services = this.$store.state.authenticatedServices;
				for (const key in services) {
					if (!services.hasOwnProperty(key)) {
						continue;
					}
					const service = services[key];
					for (const account_id in service.available_accounts) {
						if (!service.available_accounts.hasOwnProperty(account_id)) {
							continue;
						}
						all_accounts[account_id] = service.available_accounts[account_id];
						if (service.service === 'twitter') {
						    twitter += Object.keys(service.available_accounts).length;
						}
					}
				}
				this.twitter_warning = twitter > 1;
				this.$log.info('All accounts: ', all_accounts);
				this.accountsCount = Object.keys(all_accounts).length;
				return all_accounts;
			},
		},

		methods: {
			resetAccountData: function () {
				if (this.is_loading) {
					this.$log.warn('Request in progress...Bail');
					return;
				}
				this.is_loading = true;
				this.$store.dispatch('fetchAJAXPromise', {
					req: 'reset_accounts',
					data: {}
				}).then(response => {
					this.is_loading = false;
				}, error => {
					this.is_loading = false;
					Vue.$log.error('Got nothing from server. Prompt user to check internet connection and try again', error)
				})
			}
		},
		components: {
			SignInBtn,
			ServiceUserTile,
			AddAccountTile
		}
	}
</script>
<style scoped>
	#rop_core .columns.py-2 .text-gray {
		margin: 0;
		line-height: normal;
	}
	
	#rop_core .input-group {
		width: 100%;
	}
	
	b {
		margin-bottom: 5px;
		display: block;
	}
	
	#rop_core .text-gray b {
		display: inline;
	}
	
	#rop_core .input-group .input-group-addon {
		padding: 3px 5px;
	}
	
	#rop_core .rop-available-accounts h5 {
		margin-bottom: 15px;
	}
	
	@media ( max-width: 600px ) {
		#rop_core .panel-body .text-gray {
			margin-bottom: 10px;
		}
		
		#rop_core .text-right {
			text-align: left;
		}
	}
</style>